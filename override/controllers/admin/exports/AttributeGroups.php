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
        $header[] = "Nb utilisation";
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
            $data[] = $group['name'];
            $data[] = $group['public_name'];
            $data[] = $group['reference'];
            $data[] = $group['quotation'];
            $data[] = $group['column'];   
            $data[] = (int)Db::getInstance()->getValue("SELECT COUNT(DISTINCT(pac.id_product_attribute)) FROM ps_product_attribute_combination pac, ps_attribute a WHERE pac.id_attribute = a.id_attribute AND  a.id_attribute_group = ".$group['id_attribute_group']);
            $data[] = 0;

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;  
        }

        $this->renderCSV("attributes_groups_".date('d-m_H-i').".csv", $csv);
    }

}