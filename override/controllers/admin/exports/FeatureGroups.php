<?php

class ExportFeatureGroups extends Export {

    /**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
        $header[] = "Nom";
        $header[] = "Nom public";
        $header[] = "SKU";
        $header[] = "colonne";
        $header[] = "supprimer";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(Feature::getFeatures(1) as $group) {

            $data = array();
            $data[] = $group['id_feature'];
            $data[] = $group['name'];
            $data[] = $group['public_name'];
            $data[] = $group['reference'];
            $data[] = $group['column'];   
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("feature_groups_".date('d-m_H-i').".csv", $csv);
    }

}