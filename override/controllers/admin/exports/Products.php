<?php

class ExportProducts extends Export {

    /**
    * Retourne l'entête des colonnes
    * @return array
    **/
    private function getHeader() {

        $header[] = "Produit ID";
        $header[] = "Déclinaison ID";
        $header[] = "Type";
        $header[] = "Référence";
        $header[] = "ID Fournisseur";
        $header[] = "Référence fournisseur";
        $header[] = "Ids catégories";
        $header[] = "ID catégorie principale";
        $header[] = "Désignation";
        $header[] = "Quantité minimale";
        $header[] = "Etat";
        $header[] = "Lien";

        return $header;
    }

	/**
    * Exporte une fichier CSV
    **/
    function export() {

        $context = Context::getContext();
        $header = $this->getHeader();

        // Liste de toutes les caractéristiques
        $sql = "SELECT agl.id_attribute_group, agl.name FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) GROUP BY agl.id_attribute_group ORDER BY ag.id_attribute_group ASC";
        $attributes = Db::getInstance()->executeS($sql);
        foreach($attributes as $group)
            $header[] = $group['name'];

        // Liste des catégories
        $category_ids = array();
        $categories = Tools::getValue('categories', array());
        foreach($categories as $id) {
            $category_ids[] = $id;

            $category = new Category($id);
            foreach($category->getChildrenWs() as $child)
                $category_ids[] = $child['id'];
        }

        $csv = implode($this->separator, $header).parent::END_OF_LINE;

        $sql = "SELECT p.id_product, (SELECT GROUP_CONCAT(id_category SEPARATOR '".$this->delimiter."') FROM ps_category_product WHERE id_product = p.id_product) AS id_categories FROM ps_product p WHERE 1";
        if($category_ids = implode(',', $category_ids))
            $sql .= " AND id_category_default IN ($category_ids)";
        if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
            $sql .= " AND id_supplier IN ($supplier_ids)";

        foreach(Db::getInstance()->executeS($sql) as $row) {
            $product = new Product($row['id_product'], true, 1, $context->shop->id);

            $data = array();
            $data[] = $product->id;
            $data[] = null;
            $data[] = parent::TYPE_PRODUCT;
            $data[] = $product->reference;
            $data[] = $product->id_supplier;
            $data[] = ProductSupplier::getProductSupplierReference($product->id, 0, $product->id_supplier);
            $data[] = $row['id_categories'];
            $data[] = $product->id_category_default;
            $data[] = $product->name;
            $data[] = $product->minimal_quantity;
            $data[] = (int)$product->active;
            $data[] = $product->link_rewrite;

            // Liste de toutes les caractéristiques
                foreach($attributes as $group)
                    $data[] = Db::getInstance()->getValue("SELECT fvl.value FROM ps_feature_value_lang fvl, ps_feature_product fp WHERE fvl.id_feature_value = fp.id_feature_value AND id_feature = ".$group['id_attribute_group']." AND fp.id_product = ".$product->id);

            $csv .= implode($this->separator, $data).parent::END_OF_LINE;

            // Déclinaisons du produit
            foreach(Combination::getCombinations($product->id) as $combination) {

                $data = array();
                $data[] = $product->id;
                $data[] = $combination->id;
                $data[] = parent::TYPE_COMBINATION;
                $data[] = $combination->reference;
                $data[] = $product->id_supplier;
                $data[] = ProductSupplier::getProductSupplierReference($product->id, $combination->id, $product->id_supplier);
                $data[] = $row['id_categories'];
                $data[] = null;
                $data[] = null;
                $data[] = $combination->minimal_quantity;
                $data[] = null;
                $data[] = null;

                // Liste de toutes les attributs
                foreach($attributes as $group)
                    $data[] = Db::getInstance()->getValue("SELECT al.name FROM ps_attribute a, ps_attribute_lang al, ps_attribute_group ag, ps_product_attribute_combination pac WHERE a.id_attribute = al.id_attribute AND al.id_attribute = pac.id_attribute AND a.id_attribute_group = ".$group['id_attribute_group']." AND pac.id_product_attribute = ".$combination->id);

                $csv .= implode($this->separator, $data).parent::END_OF_LINE;
            }
        }

        $this->renderCSV("produits_".date('d-m_H-i').".csv", $csv);
    }

}