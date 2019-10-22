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

        $header[] = "ID produit";
        $header[] = "ID déclinaison";
        $header[] = "Type";
        //$header[] = "Référence du bundle";
        $header[] = "Référence";
        $header[] = "Ids catégories";
        $header[] = "ID catégorie principale";
        //$header[] = "Catégories (noms)";
        //$header[] = "Catégorie Principale (nom)";
        $header[] = "Désignation";
        $header[] = "Quantité minimale";
        $header[] = "Stock";
        $header[] = "Seuil d'alerte";
        $header[] = "Etat";
        $header[] = "Rollcash";
        //$header[] = "Rollplus";
        $header[] = "Description courte";
        $header[] = "Description longue";
        $header[] = "Lien";
        $header[] = "META : titre";
        $header[] = "META : description";
        $header[] = "META : mots clés";
        $header[] = "Fournisseur";
        // $header[] = "Référence Fournisseur";
        // $header[] = "ID images";
        // $header[] = "URL images";
        // $header[] = "Désignation";
        // $header[] = "Commentaire 1";
        // $header[] = "Commentaire 2";

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
            $data[] = $row['id_categories'];
            $data[] = $product->id_category_default;
            $data[] = $product->name;
            $data[] = $product->minimal_quantity;
            $data[] = $product->quantity;
            $data[] = $product->low_stock_threshold ?? 0;
            $data[] = (int)$product->active;
            $data[] = (float)$product->rollcash;
            //$data[] = "Rollplus";
            $data[] = $product->description_short;
            $data[] = $product->description;
            $data[] = $product->link_rewrite;
            $data[] = $product->meta_title;
            $data[] = $product->meta_description;
            $data[] = $product->meta_keywords;
            $data[] = $product->id_supplier;
            // $data[] = "ID images";
            // $data[] = "URL images";
            // $data[] = "Commentaire 1";
            // $data[] = "Commentaire 2";

            $csv .= implode($this->separator, $data).self::END_OF_LINE;

            // Déclinaisons du produit
            foreach(Combination::getCombinations($product->id) as $combination) {

                $data = array();
                $data[] = $product->id;
                $data[] = $combination->id;
                $data[] = self::TYPE_COMBINATION;
                $data[] = $combination->reference;
                $data[] = $row['id_categories'];
                $data[] = null;
                $data[] = null;
                $data[] = $combination->minimal_quantity;
                $data[] = $combination->quantity;
                $data[] = $combination->low_stock_threshold ?? 0;
                $data[] = null;
                $data[] = (float)$combination->rollcash;
                //$data[] = "Rollplus";
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                $data[] = null;
                // $data[] = "ID images";
                // $data[] = "URL images";
                // $data[] = "Commentaire 1";
                // $data[] = "Commentaire 2";

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

                // Produit
                if($row[2] == self::TYPE_PRODUCT) {
                    $product = new Product($row[0], true, 1, $this->context->shop->id);
                    $update = (bool)$product->id;

                    if($row[0]) {
                        $product->force_id = true;
                        $product->id = $row[0];
                    }

                    $product->reference = $row[3];
                    $product->id_category_default = (int)$row[5];
                    $product->name = $row[6];
                    $product->minimal_quantity = $row[7] ?? 1;
                    $product->quantity = (int)$row[8];
                    $product->low_stock_threshold = (int)$row[9];
                    $product->low_stock_alert = false;
                    $product->active = (bool)$row[10];
                    $product->rollcash = (float)$row[11];
                    //$data[] = "Rollplus";
                    $product->description_short = $row[12];
                    $product->description = $row[13];
                    $product->link_rewrite = $row[14];
                    $product->meta_title = $row[15];
                    $product->meta_description = $row[16];
                    $product->meta_keywords = $row[17];
                    $product->id_supplier = (int)$row[18];
                    $product->price = $product->price ?? 0;

                    if($update)
                        $product->save();
                    else
                        $product->add();

                    // Catégories
                    $ids = explode($this->delimiter, $row[4]);
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
                if($row[2] == self::TYPE_COMBINATION) {
                    $combination = new Combination($row[1]);
                    $update = (bool)$combination->id;

                    if($row[1]) {
                        $combination->force_id = true;
                        $combination->id = $row[1];
                    }

                    $combination->id_product = $row[0];
                    $combination->reference = $row[3];
                    $combination->minimal_quantity = $row[7] ?? 1;
                    $combination->quantity = $row[8];
                    $combination->low_stock_threshold = $row[9];

                    if($update)
                        $combination->save();
                    else
                        $combination->add();

                    // Récupération des attributs à ajouter
                    $values = array();
                    for($x=17; $x<count($row); $x++)
                        if($row[$x])
                            $values[] = "'".pSql($row[$x])."'";
                    $values = implode(',', $values);

                    // Ajout des nouveaux attributs
                    if($values) {
                        $ids = Db::getInstance()->executeS("SELECT DISTINCT(id_attribute) FROM ps_attribute_lang WHERE id_lang = 1 AND name IN ($values)");
                        $ids = array_map(function($e) { return $e['id_attribute']; }, $ids);
                        $combination->setAttributes($ids);
                    }
                    
                }

                fclose($handle);
                $this->confirmations[] = "Import terminé";
            }
        }
    }

    /**
    * Export des prix
    **/
    private function exportPrices() {

        $header[] = 'ID prix';
        $header[] = 'ID produit';
        $header[] = 'ID déclinaison';
        $header[] = 'ID boutique';
        $header[] = 'ID groupe client';
        $header[] = 'ID client';
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