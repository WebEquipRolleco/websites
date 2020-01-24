<?php

class ExportAttributeValues extends Export {
	
	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
        $header[] = "ID groupe";
        $header[] = "SKU";
        $header[] = "Valeur";
        $header[] = "supprimer";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(Attribute::getAttributes(1) as $attribute) {

            $data = array();
            $data[] = $attribute['id_attribute'];
            $data[] = $attribute['id_attribute_group'];
            $data[] = $attribute['value_reference'];
            $data[] = $attribute['name']; 
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("attributes_values_".date('d-m_H-i').".csv", $csv);
    }

}