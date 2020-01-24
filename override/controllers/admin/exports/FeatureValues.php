<?php

class ExportFeatureValues extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
        $header[] = "ID groupe";
        $header[] = "SKU";
        $header[] = "Valeur";
        $header[] = "Nb utilisation";
        $header[] = "supprimer";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(FeatureValue::getAllValues(1) as $value) {

            $data = array();
            $data[] = $value['id_feature_value'];
            $data[] = $value['id_feature'];
            $data[] = $value['reference'];
            $data[] = $value['value']; 
            $data[] = (int)Db::getInstance()->getValue("SELECT COUNT(*) FROM ps_feature_product WHERE id_feature_value = ".$value['id_feature_value']);
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("features_values_".date('d-m_H-i').".csv", $csv);
    }

}