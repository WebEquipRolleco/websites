<?php

class ExportProductURL extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
        $header[] = "Type";
        $header[] = "Référence";
        $header[] = "Nom";
        $header[] = "URL";

        return $header;
    }

    /**
    * Exporte une fichier CSV
    **/
    function export() {

        $context = Context::getContext();
        $header = $this->getHeader();

        $csv = implode($this->separator, $header).parent::END_OF_LINE;
        $link = new Link();

        $sql = "SELECT id_product FROM ps_product WHERE 1";
        if($category_ids = implode(',', Tools::getValue('categories', array())))
            $sql .= " AND id_category_default IN ($category_ids)";
        if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
            $sql .= " AND id_supplier IN ($supplier_ids)";
        if($status = Tools::getValue('status_type')) {
            if($status == Export::ACTIVE_PRODUCTS_ONLY)
                $sql .= " AND active = 1";
            if($status == Export::INACTIVE_PRODUCTS_ONLY)
                $sql .= " AND active = 0";
        }

        foreach(Db::getInstance()->executeS($sql) as $row) {
            $product = new Product($row['id_product'], true, 1, $context->shop->id);

            $data = array();
            $data[] = $product->id;
            $data[] = "Produit";
            $data[] = $product->reference;
            $data[] = $product->name;
            $data[] = $link->getProductLink($product);

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;

            // Déclinaisons du produit
            foreach(Combination::getCombinations($product->id) as $combination) {

                $data = array();
                $data[] = $combination->id;
                $data[] = "Déclinaison";
                $data[] = $combination->reference;
                $data[] = $product->name;
                $data[] = $link->getProductLink($product);

                $csv .= implode($this->separator, $data).parent::END_OF_LINE;
            }
        }

        $this->renderCSV("produits_".date('d-m_H-i').".csv", $csv);
    }
}