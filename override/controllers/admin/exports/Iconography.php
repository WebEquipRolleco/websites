<?php

class ExportIconography extends Export {

	/**
    * Retourne l'entête des colonnes
    * @return array
    **/
    public function getHeader() {

        $header[] = "ID";
        $header[] = "Nom";
        $header[] = "Titre";
        $header[] = "URL";
        $header[] = "Hauteur image";
        $header[] = "Largeur image";
        $header[] = "IDs produits liste blanche (x,y,z...)";
        $header[] = "IDs produits liste noire (x,y,z...)";
        $header[] = "IDs catégories liste blanche (x,y,z...)";
        $header[] = "IDs catégories liste noire (x,y,z...)";
        $header[] = "IDs fournisseurs liste blanche (x,y,z...)";
        $header[] = "IDs fournisseurs liste noire (x,y,z...)";
        $header[] = "Position";
        $header[] = "statut";
        $header[] = "IDs Boutique (x,y,z...)";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $csv = implode($this->separator, $this->getHeader()).parent::END_OF_LINE;

        foreach(ProductIcon::getList(false, false) as $icon) {

            $data = array();
            $data[] = $icon->id;
            $data[] = $icon->name;
            $data[] = $icon->title;
            $data[] = $icon->url;
            $data[] = $icon->height;
            $data[] = $icon->width;
            $data[] = $icon->product_white_list;
            $data[] = $icon->product_black_list;
            $data[] = $icon->category_white_list;
            $data[] = $icon->category_black_list;
            $data[] = $icon->supplier_white_list;
            $data[] = $icon->supplier_black_list;
            $data[] = $icon->position;
            $data[] = $icon->active;
            $data[] = implode(',', $icon->getShops());

            $csv .= implode($this->separator, $data).parent::END_OF_LINE; 
        }

        $this->renderCSV("iconography_".date('d-m_H-i').".csv", $csv);
    }

}