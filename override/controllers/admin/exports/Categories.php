<?php

class ExportCategories extends Export {
	
	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
        $header[] = "Nom";
        $header[] = "Boutiques (x,y,z...)";
        $header[] = "Boutique par défault";
        $header[] = "Boutique parent";
        $header[] = "Active";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(Category::getCategories(1, false) as $category) { 
            $category = array_pop($category)['infos'];

            $data = array();
            $data[] = $category['id_category'];
            $data[] = $category['name'];
            $data[] = implode($this->delimiter, array_map(function($e) { return $e['id_shop']; }, Category::getShopsByCategory($category['id_category'])));
            $data[] = $category['id_shop_default']; 
            $data[] = $category['id_parent']; 
            $data[] = $category['active'];

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("categories_".date('d-m_H-i').".csv", $csv);
    }

}