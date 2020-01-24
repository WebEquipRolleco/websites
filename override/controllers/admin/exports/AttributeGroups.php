<?php

class ExportAttributeGroups extends Export {

    /**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "ID";
        $header[] = "Nom";
        $header[] = "Nom public";
        $header[] = "SKU";
        $header[] = "devis";
        $header[] = "colonne";
        $header[] = "supprimer";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(AttributeGroup::getAttributesGroups(1) as $group) {

            $data = array();
            $data[] = $group['id_attribute_group'];
            $data[] = utf8_encode($group['name']);
            $data[] = utf8_encode($group['public_name']);
            $data[] = $group['reference'];
            $data[] = $group['quotation'];
            $data[] = $group['column'];   
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("attributs_groups_".date('d-m_H-i').".csv", $csv);
    }

}