<?php

class ExportProductsWithoutPrices extends Export {

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
        return "SELECT DISTINCT(id_product) FROM ps_product WHERE id_product NOT IN (SELECT id_product FROM ps_specific_price) AND id_product NOT IN (SELECT id_product FROM ps_product_attribute) AND active = 1 AND reference <> ''";
    }

    /**
    * Compte le nombre de lignes concernées
    **/
    public function count() {

        Db::getInstance()->execute($this->getSql());
        return Db::getInstance()->numRows();
    }

    /**
    * Désactive les produits concernés
    **/
    public function deactivate() {

        $ids = implode(',', array_map(function($e) { return $e['id_product']; }, Db::getInstance()->executeS($this->getSQL())));
        Db::getInstance()->execute("UPDATE ps_product SET active = 0 WHERE id_product IN ($ids)");
    }

    /**
    * Exporte une fichier CSV
    **/
	public function export() {

        $suppliers = array();

        $context = Context::getContext();
        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(Db::getInstance()->executeS($this->getSQL()) as $row) {
            
            $product = new Product($row['id_product'], true, 1);

            if($product->id_supplier and !isset($suppliers[$product->id_supplier])) 
                $suppliers[$product->id_supplier] = new Supplier($product->id_supplier, 1);

            $data = array();
            $data[] = null;
            $data[] = $product->id;
            $data[] = null;
            $data[] = $product->reference;
            $data[] = null;
            $data[] = $product->reference;
            $data[] = pSql($product->name);
            $data[] = 1;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = $product->rollcash;
            $data[] = null;
            $data[] = null;
            $data[] = isset($suppliers[$product->id_supplier]) ? $suppliers[$product->id_supplier]->name : null;
            $data[] = Product::getSupplierReference($price->id_product);
            $data[] = $product->batch;
            $data[] = str_replace('.', ',', $product->custom_ecotax);
            $data[] = "oui";
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