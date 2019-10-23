<?php

class AdminImportExportControllerCore extends AdminController {

	const SEPARATOR = "@";
	const DELIMITER = "|";
	const END_OF_LINE = "\n";

	const TYPE_PRODUCT = "Produit";
	const TYPE_COMBINATION = "Déclinaison";

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
        $data[] = "buying_price";
        $data[] = "delivery_fees";
        $data[] = "ids_category";
        $data[] = "id_main_category";
        $data[] = "name";
        $data[] = "min_quantity";
        $data[] = "stock";
        $data[] = "min_threshold";
        $data[] = "state";
        $data[] = "rollcash";
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
        $data[] = 'id_product_attribute';
        $data[] = 'id_shop';
        $data[] = 'id_group';
        $data[] = 'id_customer';
        $data[] = 'price';
        $data[] = 'from_quantity';
        $data[] = 'reduction';
        $data[] = 'reduction_type';
        $data[] = 'from';
        $data[] = 'to';

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
        $header[] = "Prix d'achat HT";
        $header[] = "Frais de port HT";
        $header[] = "Ids catégories";
        $header[] = "ID catégorie principale";
        $header[] = "Désignation";
        $header[] = "Quantité minimale";
        $header[] = "Stock";
        $header[] = "Seuil d'alerte";
        $header[] = "Etat";
        $header[] = "Rollcash";
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
        $sql = "SELECT DISTINCT(agl.name) FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
        foreach(Db::getInstance()->executeS($sql) as $group)
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
            $data[] = $product->wholesale_price;
            $data[] = $product->delivery_fees;
            $data[] = $row['id_categories'];
            $data[] = $product->id_category_default;
            $data[] = $product->name;
            $data[] = $product->minimal_quantity;
            $data[] = $product->quantity;
            $data[] = $product->low_stock_threshold ?? 0;
            $data[] = (int)$product->active;
            $data[] = (float)$product->rollcash;
            $data[] = $product->description_short;
            $data[] = $product->description;
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
                $data[] = ProductSupplier::getProductSupplierPrice($product->id, $combination->id, $product->id_supplier);
                $data[] = $combination->delivery_fees;
                $data[] = $row['id_categories'];
                $data[] = null;
                $data[] = null;
                $data[] = $combination->minimal_quantity;
                $data[] = $combination->quantity;
                $data[] = $combination->low_stock_threshold ?? 0;
                $data[] = null;
                $data[] = (float)$combination->rollcash;
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
                $sql = "SELECT g.id_attribute_group, l.name FROM ps_attribute_group g LEFT JOIN ps_attribute a ON (a.id_attribute_group = g.id_attribute_group AND a.id_attribute IN (SELECT id_attribute FROM `ps_product_attribute_combination` WHERE id_product_attribute = ".$combination->id.")) LEFT JOIN ps_attribute_lang l ON (a.id_attribute = l.id_attribute AND l.id_lang = 1) ORDER BY g.id_attribute_group ASC";
                foreach(Db::getInstance()->executeS($sql) as $row)
                    $data[] = $row['name'];

                $csv .= implode($this->separator, $data).self::END_OF_LINE;
            }
        }

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
                    $product->quantity = (int)$row["stock"];
                    $product->low_stock_threshold = (int)$row["min_threshold"];
                    $product->low_stock_alert = false;
                    $product->active = (bool)$row["state"];
                    $product->rollcash = (float)$row["rollcash"];
                    $product->description_short = $row["short_description"];
                    $product->description = $row["description"];
                    $product->link_rewrite = $row["link_rewrite"];
                    $product->meta_title = $row["meta_title"];
                    $product->meta_description = $row["meta_description"];
                    $product->meta_keywords = $row["meta_keywords"];
                    $product->id_supplier = (int)$row["id_supplier"];
                    $product->comment_1 = $row["comment_1"];
                    $product->comment_2 = $row["comment_2"];
                    $product->wholesale_price = $row["buying_price"];
                    $product->delivery_fees = $row["delivery_fees"];
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
                    $combination->quantity = $row["stock"];
                    $combination->low_stock_threshold = $row["min_threshold"];
                    $combination->delivery_fees = $row["delivery_fees"];
                    $combination->low_stock_alert = false;

                    if($update)
                        $combination->save();
                    else
                        $combination->add();

                    // Gestion des fournisseurs
                    ProductSupplier::removeCombination($combination->id);
                    if($row['id_supplier'] and ($row["supplier_reference"] or $row['buying_price'])) {

                        $supplier = new ProductSupplier();
                        $supplier->id_product = $row['id_product'];
                        $supplier->id_product_attribute = $row['id_product_attribute'];
                        $supplier->id_supplier = $row['id_supplier'];
                        $supplier->product_supplier_reference = $row["supplier_reference"];
                        $supplier->id_currency = 1;
                        $supplier->product_supplier_price_te = (float)$row['buying_price'] ?? 0;

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
                
            }

            fclose($handle);
            $this->confirmations[] = "Import terminé";
        }
    }

    /**
    * Export des prix
    **/
    private function exportPrices() {

        $header[] = 'prix ID';
        $header[] = 'Produit ID';
        $header[] = 'Déclinaison ID';
        $header[] = 'Boutique ID';
        $header[] = 'Groupe client ID';
        $header[] = 'Client ID';
        $header[] = 'Prix';
        $header[] = 'Quantité de départ';
        $header[] = 'Réduction';
        $header[] = 'Type de réduction';
        $header[] = 'Date de départ';
        $header[] = 'Date de fin';

        $csv = implode($this->separator, $header).self::END_OF_LINE;

        $sub_sql = "SELECT p.id_product FROM ps_product p WHERE 1";
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
            $data[] = $price->id_shop ?? 0;
            $data[] = $price->id_group;
            $data[] = $price->id_customer;
            $data[] = $price->price;
            $data[] = $price->from_quantity;
            $data[] = $price->reduction;
            $data[] = $price->reduction_type;
            $data[] = $price->from;
            $data[] = $price->to;

            $csv .= implode($this->separator, $data).self::END_OF_LINE;
        }

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
                $row = array_combine($this->getPricesColumns(), $row);

                $price = new SpecificPrice($row['id_specific_price']);
                $update = (bool)$price->id;

                $price->id = $row['id_specific_price'];
                $price->id_product = $row['id_product'];
                $price->id_product_attribute = $row['id_product_attribute'];
                $price->id_shop = $row['id_shop'];
                $price->id_group = $row['id_group'];
                $price->id_customer = $row['id_customer'];
                $price->price = $row['price'];
                $price->from_quantity = $row['from_quantity'];
                $price->reduction = $row['reduction'];
                $price->reduction_type = $row['reduction_type'];
                $price->from = $row['from'];
                $price->to = $row['to'];

                if($update)
                    $price->save();
                else
                    $price->add();
            }

            fclose($handle);
            $this->confirmations[] = "Import terminé";
        }
    }

}