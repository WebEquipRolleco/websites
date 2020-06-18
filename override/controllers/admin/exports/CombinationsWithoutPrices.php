<?php

class ExportCombinationsWithoutPrices extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = 'prix ID';
        $header[] = 'Produit ID';
        $header[] = 'Declinaison ID';
        $header[] = 'Reference produit *';
        $header[] = 'Reference declinaison *';
        $header[] = 'Reference absolue *';
        $header[] = 'Designation *';
        $header[] = 'Quantite de depart';
        $header[] = "Prix avant réduction (ecotaxe comprise)";
        $header[] = "Prix de vente (ecotaxe comprise)";
        $header[] = "Prix d'achat unitaire HT";
        $header[] = 'Frais de port unitaire HT';
        $header[] = 'Marge *';
        $header[] = 'Rollcash';
        $header[] = 'Commentaire 1';
        $header[] = 'Commentaire 2';
        $header[] = 'Nom fournisseur *';
        $header[] = 'Reference fournisseur *';
        $header[] = "Lot";
        $header[] = "Ecotaxe";
        $header[] = 'Actif *';
        $header[] = 'Date de depart';
        $header[] = 'Date de fin';
        $header[] = 'Groupe client ID';
        $header[] = 'Client ID';
        $header[] = 'Boutique ID';
        $header[] = "Supprimer";

        return $header;
    }

    /** 
    * Retourne la requête SQL 
    **/
    private function getSQL() {
        return "SELECT DISTINCT(pa.id_product_attribute) FROM ps_product_attribute pa, ps_product p WHERE p.id_product = pa.id_product AND pa.id_product_attribute NOT IN (SELECT id_product_attribute FROM ps_specific_price) AND p.active = 1";
    }

    public function count() {

        Db::getInstance()->execute($this->getSql());
        return Db::getInstance()->numRows();
    }

    /**
    * Exporte une fichier CSV
    **/
	public function export() {

        $suppliers = array();

        $context = Context::getContext();
        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(Db::getInstance()->executeS($this->getSQL()) as $row) {
            
            $combination = new Combination($row['id_product_attribute'], 1);

            if($combination->getProduct()->id_supplier and !isset($suppliers[$combination->getProduct()->id_supplier])) 
                $suppliers[$combination->getProduct()->id_supplier] = new Supplier($combination->getProduct()->id_supplier, 1);

            $data = array();
            $data[] = null;
            $data[] = $combination->getProduct()->id;
            $data[] = $combination->id;
            $data[] = $combination->getProduct()->reference;
            $data[] = $combination->reference;
            $data[] = $combination->reference;
            $data[] = pSql($combination->getProduct()->name);
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = $combination->getProduct()->rollcash;
            $data[] = null;
            $data[] = null;
            $data[] = isset($suppliers[$combination->getProduct()->id_supplier]) ? $suppliers[$combination->getProduct()->id_supplier]->name : null;
            $data[] = Product::getSupplierReference($combination->id_product, $combination->id);
            $data[] = $combination->batch;
            $data[] = str_replace('.', ',', $combination->custom_ecotax);
            $data[] = 'oui';
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = 0;
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }

        $this->renderCSV("produits_sans_prix_".date('d-m_H-i').".csv", $csv);
	}

}