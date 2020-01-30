<?php

class ImportProducts extends Import {

	private $current_id_product;

	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = "id_product";
        $data[] = "id_product_attribute";
        $data[] = "type";
        $data[] = "reference";
        $data[] = "id_supplier";
        $data[] = "supplier_reference";
        $data[] = "ids_category";
        $data[] = "id_main_category";
        $data[] = "name";
        $data[] = "min_quantity";
        $data[] = "state";
        $data[] = "link_rewrite";

        // Liste de toutes les caractéristiques
        $sql = "SELECT DISTINCT(ag.id_attribute_group) FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = $row['id_attribute_group'];

        return $data;
    }

    /**
    * Import du fichier
    **/
    public function import() {

    	$this->openFile();  
        while($row = $this->getNextRow()) {
            if(is_array($row)) {

                $nb_columns = count($this->getColumns());
                $nb_values = count($row);

                if($nb_columns != $nb_values)
                    for($x=0; $x<($nb_columns - $nb_values); $x++)
                        $row[] = null;
           
                $row = array_combine($this->getColumns(), $row);

                // Produit
                if(trim(strtolower($row["type"])) == strtolower(self::TYPE_PRODUCT))
                   	$this->handleProduct($row);

                // Déclinaison
                if(trim(strtolower($row["type"])) == strtolower(self::TYPE_COMBINATION))
                    $this->handleCombination($row);  

            }
            else
                $this->has_errors = true;
        }

        return true;
    }

    /**
    * Création / Modification des produits
    * @param array $row
    **/
    private function handleProduct($row) {
    	
    	$product = new Product($row["id_product"], true, 1, $this->context->shop->id);
        $update = !empty($product->id);

        $product->id = $row["id_product"];
        $product->reference = $row["reference"];
        $product->id_supplier = (int)$row["id_supplier"];
        $product->supplier_reference = $row["supplier_reference"];
        $product->id_category_default = (int)$row["id_main_category"];
        $product->name = $row["name"];
        $product->minimal_quantity = $row["min_quantity"] ?? 1;
        $product->quantity = 99999;
        $product->low_stock_threshold = 0;
        $product->low_stock_alert = false;
        $product->active = (bool)$row["state"];
        $product->link_rewrite = $row["link_rewrite"];
        $product->price = 0;
        $product->ecotax = 0;

        $product->record($update);
        $this->nb_lines++;

        // Sauvegqrder l'ID du produit pour la création de déclinaisons
        $this->current_id_product = $product->id;

        // Récupération des caractéristiques à ajouter
        $ids = array();
        $sql = "SELECT DISTINCT(f.id_feature) FROM ps_feature f LEFT JOIN ps_feature_lang fl ON (f.id_feature = fl.id_feature AND fl.id_lang = 1) ORDER BY f.id_feature ASC";
        foreach(Db::getInstance()->executeS($sql) as $id) {

            if(isset($row[$id['id_feature']]) and !empty($row[$id['id_feature']])) {

                $result = Db::getInstance()->getRow("SELECT fv.id_feature AS id, fv.id_feature_value FROM ps_feature_value fv, ps_feature_value_lang fvl WHERE fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = 1 AND fvl.value = '".$row[$id['id_feature']]."' AND fv.id_feature = ".$id['id_feature']);
                if($result) $ids[] = $result;
            }
        }

        // Ajout des nouveaux attributs
        $product->setWsProductFeatures($ids);

        // Gestion des fournisseurs
        ProductSupplier::removeProduct($product->id);
        if($row['id_supplier'] and $row["supplier_reference"]) {

            $supplier = new ProductSupplier();
            $supplier->id_product = $product->id;
            $supplier->id_product_attribute = 0;
            $supplier->id_supplier = $row['id_supplier'];
            $supplier->product_supplier_reference = $row["supplier_reference"];
            $supplier->id_currency = 1;

            $supplier->save();
        }

        // Catégories
        $ids = explode($this->delimiter, $row["ids_category"]);
        if(!empty($ids)) {

            $position = 1;
            Db::getInstance()->execute("DELETE FROM ps_category_product WHERE id_product = ".$product->id);
            foreach($ids as $id) {
                if($id) {
                    Db::getInstance()->execute("INSERT INTO ps_category_product VALUES (".$id.", ".$product->id.", ".$position.")");
                    $position++;
                }
            }
        }
    }

    /**
    * Création / Modification des déclinaisons
    * @param array $row
    **/
    private function handleCombination($row) {

    	$combination = new Combination($row["id_product_attribute"]);
        $update = !empty($combination->id);

        $combination->id = $row["id_product_attribute"];
        $combination->id_product = ($row["id_product"] ? $row["id_product"] : $this->current_id_product);
        $combination->reference = $row["reference"];
        $combination->minimal_quantity = (int)$row["min_quantity"] ?? 1;
        $combination->quantity = 99999;
        $combination->low_stock_threshold = 0;
        $combination->low_stock_alert = false;  
        $combination->ecotax = 0;

        $combination->record($update);
        $this->nb_lines++;

        // Gestion des fournisseurs
        ProductSupplier::removeCombination($combination->id);
        if($row['id_supplier'] and $row["supplier_reference"]) {

            $supplier = new ProductSupplier();
            $supplier->id_product = $combination->id_product;
            $supplier->id_product_attribute = $combination->id;
            $supplier->id_supplier = $row['id_supplier'];
            $supplier->product_supplier_reference = $row["supplier_reference"];
            $supplier->id_currency = 1;

            $supplier->save();
        }

        // Récupération des attributs à ajouter
        $ids = array();
        $sql = "SELECT DISTINCT(ag.id_attribute_group) FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
        foreach(Db::getInstance()->executeS($sql) as $id)
            if(isset($row[$id['id_attribute_group']]) and !empty($row[$id['id_attribute_group']]))
                if($id = Db::getInstance()->getValue("SELECT a.id_attribute FROM ps_attribute a, ps_attribute_lang al WHERE a.id_attribute = al.id_attribute AND al.id_lang = 1 AND al.name = '".$row[$id['id_attribute_group']]."' AND a.id_attribute_group = ".$id['id_attribute_group']))
                    $ids[] = $id;
                
        // Ajout des nouveaux attributs
        if(!empty($ids))
            $combination->setAttributes($ids);

    }

}