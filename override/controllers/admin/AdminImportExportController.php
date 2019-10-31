<?php

class AdminImportExportControllerCore extends AdminController {

	const SEPARATOR = "@";
	const DELIMITER = "|";
	const END_OF_LINE = "\n";

	const TYPE_PRODUCT = "Produit";
	const TYPE_COMBINATION = "Déclinaison";

    const ACTIVE_PRODUCTS_ONLY = 1;
    const INACTIVE_PRODUCTS_ONLY = 2;

	private $separator;
	private $delimiter;

	public function __construct() {
        
        $this->bootstrap = true;
        $this->separator = Tools::getValue('separator', self::SEPARATOR);
        $this->delimiter = Tools::getValue('delimiter', self::DELIMITER);

        parent::__construct();
    }

    public function initContent() {
    	$this->context->controller->addjQueryPlugin('select2');

    	$this->context->smarty->assign('separator', self::SEPARATOR);
    	$this->context->smarty->assign('delimiter', self::DELIMITER);
    	$this->context->smarty->assign('suppliers', Supplier::getSuppliers(1));
    	$this->context->smarty->assign('categories', Category::getAllCategoriesName(null, 1));

    	parent::initContent();
    }

    public function postProcess() {

        switch(Tools::getValue('action')) {
            
            case 'export_products':
                $this->exportProducts();
            break;
            
            case 'import_products':
                $this->importProducts();
            break;

            case 'export_prices':
                $this->exportPrices();
            break;

            case 'import_prices':
                $this->importPrices();
            break;

        }
    }

    /**
    * Header des produits
    **/
    private function getProductsColumns() {

        $data[] = "id_product";
        $data[] = "id_product_attribute";
        $data[] = "type";
        $data[] = "reference";
        $data[] = "supplier_reference";
        $data[] = "ids_category";
        $data[] = "id_main_category";
        $data[] = "name";
        $data[] = "min_quantity";
        $data[] = "state";
        $data[] = "short_description";
        $data[] = "description";
        $data[] = "link_rewrite";
        $data[] = "meta_title";
        $data[] = "meta_description";
        $data[] = "meta_keywords";
        $data[] = "id_supplier";
        $data[] = "comment_1";
        $data[] = "comment_2";

        // Liste de toutes les caractéristiques
        $sql = "SELECT DISTINCT(ag.id_attribute_group) FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = $row['id_attribute_group'];

        return $data;
    }

    /**
    * Header des prix
    **/
    private function getPricesColumns() {

        $data[] = 'id_specific_price';
        $data[] = 'id_product';
        $data[] = 'id_combination';
        $data[] = '_product_reference';
        $data[] = '_combination_reference';
        $data[] = '_name';
        $data[] = 'min_quantity';
        $data[] = "price";
        $data[] = "reduced_price";
        $data[] = "buying_price";
        $data[] = 'delivery_fees';
        $data[] = '_margin';
        $data[] = 'rollcash';
        $data[] = 'comment_1';
        $data[] = 'comment_2';
        $data[] = 'id_supplier';
        $data[] = '_supplier_reference';
        $data[] = "batch";
        $data[] = "ecotax";
        $data[] = '_active';
        $data[] = 'from';
        $data[] = 'to';
        $data[] = 'id_group';
        $data[] = 'id_customer';
        $data[] = 'id_shop';

        return $data;
    }

    /**
    * Export des produits
    **/
    private function exportProducts() {

        $header[] = "Produit ID";
        $header[] = "Déclinaison ID";
        $header[] = "Type";
        $header[] = "Référence";
        $header[] = "Référence fournisseur";
        $header[] = "Ids catégories";
        $header[] = "ID catégorie principale";
        $header[] = "Désignation";
        $header[] = "Quantité minimale";
        $header[] = "Etat";
        $header[] = "Description courte";
        $header[] = "Description longue";
        $header[] = "Lien";
        $header[] = "META : titre";
        $header[] = "META : description";
        $header[] = "META : mots clés";
        $header[] = "ID Fournisseur";
        $header[] = "Commentaire 1";
        $header[] = "Commentaire 2";

        // Liste de toutes les caractéristiques
        $sql = "SELECT agl.id_attribute_group, agl.name FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
        $attributes = Db::getInstance()->executeS($sql);
        foreach($attributes as $group)
            $header[] = $group['name'];

        $csv = implode($this->separator, $header).self::END_OF_LINE;

        $sql = "SELECT p.id_product, (SELECT GROUP_CONCAT(id_category SEPARATOR '".$this->delimiter."') FROM ps_category_product WHERE id_product = p.id_product) AS id_categories FROM ps_product p WHERE 1";
        if($category_ids = implode(',', Tools::getValue('categories', array())))
            $sql .= " AND id_category_default IN ($category_ids)";
        if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
            $sql .= " AND id_supplier IN ($supplier_ids)";

        foreach(Db::getInstance()->executeS($sql) as $row) {
            $product = new Product($row['id_product'], true, 1, $this->context->shop->id);

            $data = array();
            $data[] = $product->id;
            $data[] = null;
            $data[] = self::TYPE_PRODUCT;
            $data[] = $product->reference;
            $data[] = $product->supplier_reference;
            $data[] = $row['id_categories'];
            $data[] = $product->id_category_default;
            $data[] = $product->name;
            $data[] = $product->minimal_quantity;
            $data[] = (int)$product->active;
            $data[] = pSql($product->description_short, true);
            $data[] = pSql($product->description, true);
            $data[] = $product->link_rewrite;
            $data[] = $product->meta_title;
            $data[] = $product->meta_description;
            $data[] = $product->meta_keywords;
            $data[] = $product->id_supplier;
            $data[] = $product->comment_1;
            $data[] = $product->comment_2;

            $csv .= implode($this->separator, $data).self::END_OF_LINE;

            // Déclinaisons du produit
            foreach(Combination::getCombinations($product->id) as $combination) {

                $data = array();
                $data[] = $product->id;
                $data[] = $combination->id;
                $data[] = self::TYPE_COMBINATION;
                $data[] = $combination->reference;
                $data[] = ProductSupplier::getProductSupplierReference($product->id, $combination->id, $product->id_supplier);
                $data[] = $row['id_categories'];
                $data[] = null;
                $data[] = null;
                $data[] = $combination->minimal_quantity;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = $product->id_supplier;
                $data[] = null;
                $data[] = null;

                // Liste de toutes les caractéristiques
                foreach($attributes as $group)
                    $data[] = Db::getInstance()->getValue("SELECT al.name FROM ps_attribute a, ps_attribute_lang al, ps_attribute_group ag, ps_product_attribute_combination pac WHERE a.id_attribute = al.id_attribute AND al.id_attribute = pac.id_product_attribute AND a.id_attribute_group = ".$group['id_attribute_group']." AND pac.id_product_attribute = ".$combination->id);

                $csv .= implode($this->separator, $data).self::END_OF_LINE;
            }
        }

        header('Content-Type: application/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="produits.csv";');
        die($csv);
    }

    /**
    * Import produits
    **/
    private function importProducts() {

        if($file = $_FILES['import_file']) {

            $handle = fopen($file['tmp_name'], 'r');
                
            // Lignes à ignorer
            for($x=0; $x<Tools::getValue('skip'); $x++)
                fgetcsv($handle, 0, $this->separator);

            while($row = fgetcsv($handle, 0, $this->separator)) {
                if(is_array($row)) {

                    $nb_columns = count($this->getProductsColumns());
                    $nb_values = count($row);

                    if($nb_columns != $nb_values)
                        for($x=0; $x<($nb_columns - $nb_values); $x++)
                            $row[] = null;
           
                    $row = array_combine($this->getProductsColumns(), $row);

                    // Produit
                    if($row["type"] == self::TYPE_PRODUCT) {
                        $product = new Product($row["id_product"], true, 1, $this->context->shop->id);
                        $update = (bool)$product->id;

                        if($row["id_product"]) {
                            $product->force_id = true;
                            $product->id = $row["id_product"];
                        }

                        $product->reference = $row["reference"];
                        $product->supplier_reference = $row["supplier_reference"];
                        $product->id_category_default = (int)$row["id_main_category"];
                        $product->name = $row["name"];
                        $product->minimal_quantity = $row["min_quantity"] ?? 1;
                        $product->quantity = 99999;
                        $product->low_stock_threshold = 0;
                        $product->low_stock_alert = false;
                        $product->active = (bool)$row["state"];
                        $product->description_short = $row["short_description"];
                        $product->description = $row["description"];
                        $product->link_rewrite = $row["link_rewrite"];
                        $product->meta_title = $row["meta_title"];
                        $product->meta_description = $row["meta_description"];
                        $product->meta_keywords = $row["meta_keywords"];
                        $product->id_supplier = (int)$row["id_supplier"];
                        $product->comment_1 = $row["comment_1"];
                        $product->comment_2 = $row["comment_2"];
                        $product->price = $product->price ?? 0;

                        if($update)
                            $product->save();
                        else
                            $product->add();

                        // Catégories
                        $ids = explode($this->delimiter, $row["ids_category"]);
                        if(!empty($ids)) {

                            $position = 1;
                            Db::getInstance()->execute("DELETE FROM ps_category_product WHERE id_product = ".$product->id);

                            foreach($ids as $id) {
                                Db::getInstance()->execute("INSERT INTO ps_category_product VALUES (".$id.", ".$product->id.", ".$position.")");
                                $position++;
                            }
                        }
                    }

                    // Déclinaison
                    if($row["type"] == self::TYPE_COMBINATION) {
                        $combination = new Combination($row["id_product_attribute"]);
                        $update = (bool)$combination->id;

                        if($row["id_product_attribute"]) {
                            $combination->force_id = true;
                            $combination->id = $row["id_product_attribute"];
                        }

                        $combination->id_product = $row["id_product"];
                        $combination->reference = $row["reference"];
                        $combination->minimal_quantity = (int)$row["min_quantity"] ?? 1;
                        $combination->quantity = 99999;
                        $combination->low_stock_threshold = 0;
                        $combination->low_stock_alert = false;

                        if($update)
                            $combination->save();
                        else
                            $combination->add();

                        // Gestion des fournisseurs
                        ProductSupplier::removeCombination($combination->id);
                        if($row['id_supplier'] and $row["supplier_reference"]) {

                            $supplier = new ProductSupplier();
                            $supplier->id_product = $row['id_product'];
                            $supplier->id_product_attribute = $row['id_product_attribute'];
                            $supplier->id_supplier = $row['id_supplier'];
                            $supplier->product_supplier_reference = $row["supplier_reference"];
                            $supplier->id_currency = 1;

                            $supplier->save();
                        }

                        // Récupération des attributs à ajouter
                        $values = array();

                        $sql = "SELECT DISTINCT(ag.id_attribute_group) FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
                        foreach(Db::getInstance()->executeS($sql) as $id)
                            if(isset($row[$id['id_attribute_group']]) and !empty($row[$id['id_attribute_group']]))
                                $values[] = "'".pSql($row[$id['id_attribute_group']])."'";
                        $values = implode(',', $values);

                        // Ajout des nouveaux attributs
                        if($values) {
                            $ids = Db::getInstance()->executeS("SELECT DISTINCT(id_attribute) FROM ps_attribute_lang WHERE id_lang = 1 AND name IN ($values)");
                            $ids = array_map(function($e) { return $e['id_attribute']; }, $ids);
                            $combination->setAttributes($ids);
                        }  
                    }

                    $this->confirmations[] = "Import terminé";
                }
                else
                   $this->errors = "Erreur lors de l'import du fichier. Merci de vérifier le type de fichier, l'encodage et les séparateurs utilisés"; 
            }

            fclose($handle);
            
        }
    }

    /**
    * Export des prix
    **/
    private function exportPrices() {

        $header[] = 'prix ID';
        $header[] = 'Produit ID';
        $header[] = 'Declinaison ID';
        $header[] = 'Reference produit *';
        $header[] = 'Reference declinaison *';
        $header[] = 'Designation';
        $header[] = 'Quantite de depart';
        $header[] = "Prix de vente / barre";
        $header[] = "Prix degressif / remise";
        $header[] = "Prix d'achat unitaire HT";
        $header[] = 'Frais de port unitaire HT';
        $header[] = 'Marge *';
        $header[] = 'Rollcash';
        $header[] = 'Commentaire 1';
        $header[] = 'Commentaire 2';
        $header[] = 'ID fournisseur';
        $header[] = 'Reference fournisseur *';
        $header[] = "Lot";
        $header[] = "Ecotaxe";
        $header[] = 'Actif *';
        $header[] = 'Date de depart';
        $header[] = 'Date de fin';
        $header[] = 'Groupe client ID';
        $header[] = 'Client ID';
        $header[] = 'Boutique ID';

        $csv = implode($this->separator, $header).self::END_OF_LINE;

        $sub_sql = "SELECT p.id_product FROM ps_product p WHERE 1";

        $status_type = Tools::getValue('status_type');
        if($status_type == self::ACTIVE_PRODUCTS_ONLY) $sub_sql .= " AND p.active = 1";
        if($status_type == self::INACTIVE_PRODUCTS_ONLY) $sub_sql .= " AND p.active = 0";

        if($category_ids = implode(',', Tools::getValue('categories', array())))
            $sub_sql .= " AND id_category_default IN ($category_ids)";
        if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
            $sub_sql .= " AND id_supplier IN ($supplier_ids)";
        $sql = "SELECT id_specific_price FROM ps_specific_price WHERE id_product IN ($sub_sql)";

        foreach(Db::getInstance()->executeS($sql) as $row) {
            $price = new SpecificPrice($row['id_specific_price']);

            $data = array();
            $data[] = $price->id;
            $data[] = $price->id_product ?? 0;
            $data[] = $price->id_product_attribute;
            $data[] = $price->getTarget() ? $price->getTarget()->reference : null;
            $data[] = $price->getCombination() ? $price->getCombination()->reference : null;
            $data[] = $price->getProduct() ? $price->getProduct()->name : null;
            $data[] = $price->from_quantity;
            $data[] = $price->getTarget() ? $price->getTarget()->price : 0;
            $data[] = (float)$price->price;
            $data[] = $price->buying_price;
            $data[] = $price->delivery_fees;
            $data[] = Tools::getMarginRate($price->buying_price + $price->delivery_fees, $price->price)."%";
            $data[] = $price->getTarget() ? $price->getTarget()->rollcash : null;
            $data[] = $price->comment_1;
            $data[] = $price->comment_2;
            $data[] = $price->getProduct() ? $price->getProduct()->id_supplier : null;
            $data[] = $price->getTarget() ? $price->getTarget()->supplier_reference : null;
            $data[] = $price->getTarget() ? $price->getTarget()->batch : null;
            $data[] = $price->getTarget() ? $price->getTarget()->ecotax : 0;
            $data[] = ($price->getProduct() and $price->getProduct()->active) ? 'oui' : 'non';
            $data[] = $price->from;
            $data[] = $price->to;
            $data[] = $price->id_group;
            $data[] = $price->id_customer;
            $data[] = $price->id_shop ?? 0;

            $csv .= implode($this->separator, $data).self::END_OF_LINE;
        }

        header('Content-Type: application/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="prix.csv";');
        die($csv);
    }

    /**
    * Import des prix
    **/
    private function importPrices() {

        if($file = $_FILES['import_file']) {

            $handle = fopen($file['tmp_name'], 'r');
                
            // Lignes à ignorer
            for($x=0; $x<Tools::getValue('skip'); $x++)
                fgetcsv($handle, 0, $this->separator);

            while($row = fgetcsv($handle, 0, $this->separator)) {
                if(is_array($row) and count($row) == count($this->getPricesColumns())) {

                    $row = array_combine($this->getPricesColumns(), $row);

                    // Mise à jour du prix spécifique
                    $price = new SpecificPrice($row['id_specific_price']);
                    $update = (bool)$price->id;

                    if($row['id_specific_price']) {
                        $price->id = $row['id_specific_price'];
                        $price->force_id = true;
                    }

                    $price->id_product = $row['id_product'];
                    $price->id_product_attribute = $row['id_combination'];
                    $price->from_quantity = $row['min_quantity'];
                    $price->comment_1 = $row['comment_1'];
                    $price->comment_2 = $row['comment_2'];
                    $price->from = $row['from'];
                    $price->to = $row['to'];
                    $price->id_shop = $row['id_shop'] ?? 0;
                    $price->id_group = $row['id_group'];
                    $price->id_customer = $row['id_customer'];
                    $price->price = $row['reduced_price'];
                    $price->buying_price = $row['buying_price'];
                    $price->delivery_fees = $row['delivery_fees'];
                    $price->id_currency = 0;
                    $price->id_country = 0;
                    $price->reduction = 0;
                    $price->reduction_type = "amount";

                    if($update)
                        $price->save();
                    else
                        $price->add();

                    // Mise à jour du produit ou de la déclinaison
                    if($price->getTarget()) {

                        $price->getTarget()->price = $row['price'];
                        $price->getTarget()->rollcash = $row['rollcash'];
                        $price->getTarget()->batch = $row['batch'];
                        $price->getTarget()->ecotax = $row['ecotax'];

                        $price->save();
                    }

                    // Mise à jour du produit
                    if($price->getProduct()) {

                        $price->getProduct()->id_supplier = $row['id_supplier'];
                        $price->getProduct()->save();
                    }

                    $this->confirmations[] = "Import terminé";
                }
                else
                    $this->errors = "Erreur lors de l'import du fichier. Merci de vérifier le type de fichier, l'encodage et les séparateurs utilisés";
            }

            fclose($handle);
        }
    }

}