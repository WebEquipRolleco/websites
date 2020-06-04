<?php

class ExportPrices extends Export {

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
    * Exporte une fichier CSV
    **/
	public function export() {

		$ids_product = array();
        $ids_combination = array();
        $suppliers = array();

        $context = Context::getContext();
        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        $sub_sql = "SELECT p.id_product FROM ps_product p WHERE 1";

        $status_type = Tools::getValue('status_type');
        if($status_type == parent::ACTIVE_PRODUCTS_ONLY) $sub_sql .= " AND p.active = 1";
        if($status_type == parent::INACTIVE_PRODUCTS_ONLY) $sub_sql .= " AND p.active = 0";

        if($category_ids = implode(',', Tools::getValue('categories', array())))
            $sub_sql .= " AND id_category_default IN ($category_ids)";
        if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
            $sub_sql .= " AND id_supplier IN ($supplier_ids)";
        $sql = "SELECT id_specific_price FROM ps_specific_price WHERE id_product IN ($sub_sql)";

        // Prix déjà existants
        foreach(Db::getInstance()->executeS($sql) as $row) {
            $price = new SpecificPrice($row['id_specific_price']);

            if($price->id_product) $ids_product[$price->id_product] = $price->id_product;
            if($price->id_product_attribute) $ids_combination[$price->id_product_attribute] = $price->id_product_attribute;

            $id_supplier = $price->getProduct() ? $price->getProduct()->id_supplier : null;
            if($id_supplier and !isset($suppliers[$id_supplier])) $suppliers[$id_supplier] = new Supplier($id_supplier, 1);

            $data = array();
            $data[] = $price->id;
            $data[] = $price->id_product ?? 0;
            $data[] = $price->id_product_attribute;
            $data[] = $price->getProduct() ? $price->getProduct()->reference : null;
            $data[] = $price->getCombination() ? $price->getCombination()->reference : null;
            $data[] = $price->getCombination() ? $price->getCombination()->reference : $price->getProduct()->reference;
            $data[] = $price->getProduct() ? $price->getProduct()->name : null;
            $data[] = $price->from_quantity;
            $data[] = str_replace('.', ',', $price->full_price);
            $data[] = str_replace('.', ',', $price->price);
            $data[] = str_replace('.', ',', $price->buying_price);
            $data[] = str_replace('.', ',', $price->delivery_fees);
            $data[] = Tools::getMarginRate($price->buying_price + $price->delivery_fees, $price->price)."%";
            $data[] = $price->getTarget() ? str_replace('.', ',', $price->getTarget()->rollcash) : 0;
            $data[] = $price->comment_1;
            $data[] = $price->comment_2;
            $data[] = isset($suppliers[$id_supplier]) ? $suppliers[$id_supplier]->name : null;
            $data[] = Product::getSupplierReference($price->id_product, $price->id_product_attribute);
            $data[] = $price->getTarget() ? $price->getTarget()->batch : null;
            $data[] = $price->getTarget() ? str_replace('.', ',', $price->getTarget()->custom_ecotax) : 0;
            $data[] = ($price->getProduct() and $price->getProduct()->active) ? 'oui' : 'non';
            $data[] = $price->from;
            $data[] = $price->to;
            $data[] = $price->id_group;
            $data[] = $price->id_customer;
            $data[] = $price->id_shop ?? 0;
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }

        // Déclinaisons sans prix
        $sql = "SELECT pa.id_product_attribute FROM ps_product_attribute pa, ps_product p WHERE p.id_product IN ($sub_sql) AND pa.id_product = p.id_product AND p.id_product";
        if(!empty($ids_combination)) $sql .= " AND pa.id_product_attribute NOT IN (".implode(',', $ids_combination).")";

        foreach(Db::getInstance()->executeS($sql) as $row) {

            $combination = new Combination($row['id_product_attribute']);
            if($combination->id_product) {
                $combination->getProduct($context->shop->id);
                $ids_product[$combination->id_product] = $combination->id_product;
            }

            $id_supplier = $combination->getProduct()->id_supplier;
            if($id_supplier and !isset($suppliers[$id_supplier])) $suppliers[$id_supplier] = new Supplier($id_supplier, 1);

            $data = array();
            $data[] = null;
            $data[] = $combination->id_product ?? 0;
            $data[] = $combination->id;
            $data[] = $combination->getProduct()->reference;
            $data[] = $combination->reference;
            $data[] = $combination->reference;
            $data[] = $combination->getProduct()->name;
            $data[] = 1;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = null;
            $data[] = str_replace('.', ',', $combination->rollcash);
            $data[] = null;
            $data[] = null;
            $data[] = isset($suppliers[$id_supplier]) ? $suppliers[$id_supplier]->name : null;
            $data[] = Product::getSupplierReference($combination->id_product, $combination->id);
            $data[] = $combination->batch;
            $data[] = str_replace('.', ',', $combination->custom_ecotax);
            $data[] = $combination->getProduct()->active ? 'oui' : 'non';
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = 0;
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }

        // Produits sans prix
        $sql = "SELECT p.id_product FROM ps_product p WHERE p.id_product IN ($sub_sql)";
        if(!empty($ids_product)) $sql .= " AND p.id_product NOT IN (".implode(',', $ids_product).")";

        foreach(Db::getInstance()->executeS($sql) as $row) {
            
            $product = new Product($row['id_product'], true, 1, $context->shop->id);
            if($product->id_supplier and !isset($suppliers[$product->id_supplier])) $suppliers[$product->id_supplier] = new Supplier($product->id_supplier, 1);

            $data = array();
            $data[] = null;
            $data[] = $product->id;
            $data[] = 0;
            $data[] = $product->reference;
            $data[] = null;
            $data[] = $product->reference;
            $data[] = $product->name;
            $data[] = 1;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = null;
            $data[] = str_replace('.', ',', $product->rollcash);
            $data[] = null;
            $data[] = null;
            $data[] = isset($suppliers[$id_supplier]) ? $suppliers[$id_supplier]->name : null;
            $data[] = Product::getSupplierReference($product->id);
            $data[] = $product->batch;
            $data[] = str_replace('.', ',', $product->custom_ecotax);
            $data[] = $product->active ? 'oui' : 'non';
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = 0;
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }

        $this->renderCSV("prix_".date('d-m_H-i').".csv", $csv);
	}

}