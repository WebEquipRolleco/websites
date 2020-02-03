<?php

class ExportProductURL extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
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

        $sql = "SELECT p.id_product, (SELECT GROUP_CONCAT(id_category SEPARATOR '".$this->delimiter."') FROM ps_category_product WHERE id_product = p.id_product) AS id_categories FROM ps_product p WHERE 1";
        if($category_ids = implode(',', Tools::getValue('categories', array())))
            $sql .= " AND id_category_default IN ($category_ids)";
        if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
            $sql .= " AND id_supplier IN ($supplier_ids)";

        foreach(Db::getInstance()->executeS($sql) as $row) {
            $product = new Product($row['id_product'], true, 1, $context->shop->id);

            $data = array();
            $data[] = $product->id;
            $data[] = $product->name;
            $data[] = $link->getProductLink($product);

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;
        }

        $this->renderCSV("produits_".date('d-m_H-i').".csv", $csv);
    }
}