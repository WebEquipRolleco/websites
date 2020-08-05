<?php

class ExportComments extends Export {

    /**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID produit";
        $header[] = "ID déclinaison";
        $header[] = "Reference absolue *";
        $header[] = "ID boutique";
        $header[] = "Commentaire 1";
        $header[] = "Commentaire 2";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(Db::getInstance()->executeS("SELECT id_product, id_shop, reference, comment_1, comment_2 FROM ps_product_shop") as $row) {

            $data = array();
            $data[] = $row['id_product'];
            $data[] = "";
            $data[] = $row['reference'];
            $data[] = $row['id_shop'];
            $data[] = utf8_decode($row['comment_1']);
            $data[] = utf8_decode($row['comment_2']);

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        foreach(Db::getInstance()->executeS("SELECT id_product, id_product_attribute, id_shop, reference, comment_1, comment_2 FROM ps_product_attribute_shop") as $row) {
            $data = array();
            $data[] = $row['id_product'];
            $data[] = $row['id_product_attribute'];
            $data[] = $row['reference'];
            $data[] = $row['id_shop'];
            $data[] = utf8_decode($row['comment_1']);
            $data[] = utf8_decode($row['comment_2']);

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("comments_".date('d-m_H-i').".csv", $csv);
    }

}