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
    private $current_id_product;

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

            case 'export_orders':
                $this->exportOrders();
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
        $data[] = "price_full";
        $data[] = "price";
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
        $sql = "SELECT agl.id_attribute_group, agl.name FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) GROUP BY agl.id_attribute_group ORDER BY ag.id_attribute_group ASC";
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
            $data[] = ProductSupplier::getProductSupplierReference($product->id, 0, $product->id_supplier);
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

            // Liste de toutes les caractéristiques
                foreach($attributes as $group)
                    $data[] = Db::getInstance()->getValue("SELECT fvl.value FROM ps_feature_value_lang fvl, ps_feature_product fp WHERE fvl.id_feature_value = fp.id_feature_value AND id_feature = ".$group['id_attribute_group']." AND fp.id_product = ".$product->id);

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
                $data[] = $combination->comment_1;
                $data[] = $combination->comment_2;

                // Liste de toutes les attributs
                foreach($attributes as $group)
                    $data[] = Db::getInstance()->getValue("SELECT al.name FROM ps_attribute a, ps_attribute_lang al, ps_attribute_group ag, ps_product_attribute_combination pac WHERE a.id_attribute = al.id_attribute AND al.id_attribute = pac.id_attribute AND a.id_attribute_group = ".$group['id_attribute_group']." AND pac.id_product_attribute = ".$combination->id);

                $csv .= implode($this->separator, $data).self::END_OF_LINE;
            }
        }

        $file_name = "produits_".date('d-m_H-i').".csv";
        header("Content-Type: application/vnd.ms-excel; name=$file_name; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
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
                        $update = !empty($product->id);

                        $product->id = $row["id_product"];
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

                        $product->record($update);

                        // Sauvegqrder l'ID du produit pour la création de déclinaisons
                        $this->current_id_product = $product->id;

                        // Récupération des caractéristiques à ajouter
                        $ids = array();
                        $sql = "SELECT DISTINCT(f.id_feature) FROM ps_feature f LEFT JOIN ps_feature_lang fl ON (f.id_feature = fl.id_feature AND fl.id_lang = 1) ORDER BY f.id_feature ASC";
                        foreach(Db::getInstance()->executeS($sql) as $id)
                            if(isset($row[$id['id_feature']]) and !empty($row[$id['id_feature']])) {


                                $result = Db::getInstance()->getRow("SELECT fv.id_feature AS id, fv.id_feature_value FROM ps_feature_value fv, ps_feature_value_lang fvl WHERE fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = 1 AND fvl.value = '".$row[$id['id_feature']]."' AND fv.id_feature = ".$id['id_feature']);
                                if($result) $ids[] = $result;
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

                    // Déclinaison
                    if($row["type"] == self::TYPE_COMBINATION) {
                        $combination = new Combination($row["id_product_attribute"]);
                        $update = !empty($combination->id);

                        $combination->id = $row["id_product_attribute"];
                        $combination->id_product = ($row["id_product"] ? $row["id_product"] : $this->current_id_product);
                        $combination->reference = $row["reference"];
                        $combination->minimal_quantity = (int)$row["min_quantity"] ?? 1;
                        $combination->comment_1 = $row["comment_1"];
                        $combination->comment_2 = $row["comment_2"];
                        $combination->quantity = 99999;
                        $combination->low_stock_threshold = 0;
                        $combination->low_stock_alert = false;  

                        $combination->record($update);

                        // Gestion des fournisseurs
                        ProductSupplier::removeCombination($combination->id);
                        if($row['id_supplier'] and $row["supplier_reference"]) {

                            $supplier = new ProductSupplier();
                            $supplier->id_product = $combination->id_product;
                            $supplier->id_product_attribute = $row['id_product_attribute'];
                            $supplier->id_supplier = $row['id_supplier'];
                            $supplier->product_supplier_reference = $row["supplier_reference"];
                            $supplier->id_currency = 1;

                            $supplier->save();
                        }

                        // Récupération des attributs à ajouter
                        $ids = array();
                        $sql = "SELECT DISTINCT(ag.id_attribute_group) FROM ps_attribute_group ag ".Shop::addSqlAssociation('attribute_group', 'ag')." LEFT JOIN ps_attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group AND id_lang = 1) ORDER BY ag.id_attribute_group ASC";
                        foreach(Db::getInstance()->executeS($sql) as $id)
                            if(isset($row[$id['id_attribute_group']]) and !empty($row[$id['id_attribute_group']])) {

                                $ids[] = Db::getInstance()->getValue("SELECT a.id_attribute FROM ps_attribute a, ps_attribute_lang al WHERE a.id_attribute = al.id_attribute AND al.id_lang = 1 AND al.name = '".$row[$id['id_attribute_group']]."' AND a.id_attribute_group = ".$id['id_attribute_group']);
                            }

                        // Ajout des nouveaux attributs
                        if(!empty($ids)) {
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

        $ids_product = array();
        $ids_combination = array();

        $header[] = 'prix ID';
        $header[] = 'Produit ID';
        $header[] = 'Declinaison ID';
        $header[] = 'Reference produit *';
        $header[] = 'Reference declinaison *';
        $header[] = 'Designation *';
        $header[] = 'Quantite de depart';
        $header[] = "Prix avant réduction";
        $header[] = "Prix de vente";
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

        // Prix déjà existants
        foreach(Db::getInstance()->executeS($sql) as $row) {
            $price = new SpecificPrice($row['id_specific_price']);

            if($price->id_product) $ids_product[$price->id_product] = $price->id_product;
            if($price->id_product_attribute) $ids_combination[$price->id_product_attribute] = $price->id_product_attribute;

            $data = array();
            $data[] = $price->id;
            $data[] = $price->id_product ?? 0;
            $data[] = $price->id_product_attribute;
            $data[] = $price->getProduct() ? $price->getProduct()->reference : null;
            $data[] = $price->getCombination() ? $price->getCombination()->reference : null;
            $data[] = $price->getProduct() ? $price->getProduct()->name : null;
            $data[] = $price->from_quantity;
            $data[] = 0;
            $data[] = str_replace('.', ',', $price->price);
            $data[] = str_replace('.', ',', $price->buying_price);
            $data[] = str_replace('.', ',', $price->delivery_fees);
            $data[] = Tools::getMarginRate($price->buying_price + $price->delivery_fees, $price->price)."%";
            $data[] = $price->getTarget() ? str_replace('.', ',', $price->getTarget()->rollcash) : 0;
            $data[] = $price->comment_1;
            $data[] = $price->comment_2;
            $data[] = $price->getProduct() ? $price->getProduct()->id_supplier : null;
            $data[] = Product::getSupplierReference($price->id_product, $price->id_product_attribute);
            $data[] = $price->getTarget() ? $price->getTarget()->batch : null;
            $data[] = $price->getTarget() ? str_replace('.', ',', $price->getTarget()->ecotax) : 0;
            $data[] = ($price->getProduct() and $price->getProduct()->active) ? 'oui' : 'non';
            $data[] = $price->from;
            $data[] = $price->to;
            $data[] = $price->id_group;
            $data[] = $price->id_customer;
            $data[] = $price->id_shop ?? 0;

            $csv .= implode($this->separator, $data).self::END_OF_LINE;
        }

        // Déclinaisons sans prix
        $sql = "SELECT pa.id_product_attribute FROM ps_product_attribute pa, ps_product p WHERE pa.id_product = p.id_product AND p.id_product";
        if(!empty($ids_combination)) $sql .= " AND pa.id_product_attribute NOT IN (".implode(',', $ids_combination).")";
        if($status_type == self::ACTIVE_PRODUCTS_ONLY) $sql .= " AND p.active = 1";
        if($status_type == self::INACTIVE_PRODUCTS_ONLY) $sql .= " AND p.active = 0";

        foreach(Db::getInstance()->executeS($sql) as $row) {

            $combination = new Combination($row['id_product_attribute']);
            if($combination->id_product) {
                $combination->getProduct($this->context->shop->id);
                $ids_product[$combination->id_product] = $combination->id_product;
            }

            $data = array();
            $data[] = null;
            $data[] = $combination->id_product ?? 0;
            $data[] = $combination->id;
            $data[] = $combination->getProduct()->reference;
            $data[] = $combination->reference;
            $data[] = $combination->getProduct()->name;
            $data[] = 1;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = null;
            $data[] = str_replace('.', ',', $combination->rollcash);
            $data[] = null;
            $data[] = null;
            $data[] = $combination->getProduct()->id_supplier;
            $data[] = Product::getSupplierReference($combination->id_product, $combination->id);
            $data[] = $combination->batch;
            $data[] = str_replace('.', ',', $combination->ecotax);
            $data[] = $combination->getProduct()->active ? 'oui' : 'non';
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = 0;

            $csv .= implode($this->separator, $data).self::END_OF_LINE;
        }

        // Produits sans prix
        $sql = "SELECT p.id_product FROM ps_product p WHERE 1";
        if(!empty($ids_product)) $sql .= " AND p.id_product NOT IN (".implode(',', $ids_product).")";
        if($status_type == self::ACTIVE_PRODUCTS_ONLY) $sql .= " AND p.active = 1";
        if($status_type == self::INACTIVE_PRODUCTS_ONLY) $sql .= " AND p.active = 0";

        foreach(Db::getInstance()->executeS($sql) as $row) {
            $product = new Product($row['id_product'], true, 1, $this->context->shop->id);

            $data = array();
            $data[] = null;
            $data[] = $product->id;
            $data[] = 0;
            $data[] = $product->reference;
            $data[] = null;
            $data[] = $product->name;
            $data[] = 1;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = 0;
            $data[] = null;
            $data[] = str_replace('.', ',', $product->rollcash);
            $data[] = null;
            $data[] = null;
            $data[] = $product->id_supplier;
            $data[] = Product::getSupplierReference($product->id);
            $data[] = $product->batch;
            $data[] = str_replace('.', ',', $product->ecotax);
            $data[] = $product->active ? 'oui' : 'non';
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = null;
            $data[] = 0;

            $csv .= implode($this->separator, $data).self::END_OF_LINE;
        }

        $file_name = "prix_".date('d-m_H-i').".csv";
        header("Content-Type: application/vnd.ms-excel; name=$file_name; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
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
                    $update = !empty($price->id);

                    $price->id = $row['id_specific_price'];
                    $price->id_product = $row['id_product'];
                    $price->id_product_attribute = $row['id_combination'];
                    $price->from_quantity = $row['min_quantity'];
                    $price->comment_1 = $row['comment_1'];
                    $price->comment_2 = $row['comment_2'];
                    $price->from = Tools::isEmptyDate($row['from']) ? date('Y-01-01 00:00:00') : $row['from'];
                    $price->to = Tools::isEmptyDate($row['to']) ? date('Y-01-01 00:00:00') : $row['to'];
                    $price->id_shop = $row['id_shop'] ?? 0;
                    $price->id_group = (int)$row['id_group'];
                    $price->id_customer = (int)$row['id_customer'];
                    $price->price = str_replace(',', '.', $row['price']);
                    $price->buying_price = str_replace(',', '.', $row['buying_price']);
                    $price->delivery_fees = str_replace(',', '.', $row['delivery_fees']);
                    $price->id_currency = 0;
                    $price->id_country = 0;
                    $price->reduction = 0;
                    $price->reduction_type = "amount";

                    $price->record($update);

                    // Mise à jour du produit ou de la déclinaison
                    if($price->getTarget()) {

                        $price->getTarget()->rollcash = str_replace(',', '.', $row['rollcash']);
                        $price->getTarget()->batch = (int)$row['batch'];
                        $price->getTarget()->ecotax = str_replace(',', '.', $row['ecotax']);

                        $price->getTarget()->save();
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

    /**
    * Export des commandes
    **/
    public function exportOrders() {

        $header[] = "Date";
        $header[] = "Commande";
        $header[] = "Référence Web";
        $header[] = "Famille";
        $header[] = "Fournisseur";
        $header[] = "Site Web";
        $header[] = "Référence fournisseur";
        $header[] = "Désignation";
        $header[] = "Quantité";
        $header[] = "Montant net";
        $header[] = "Prix d'achat";
        $header[] = "Marge";
        $header[] = "Taux de marge";
        $header[] = "Cumul CA";
        $header[] = "Nbr de commande";
        $header[] = "Num client Web";
        $header[] = "Date d'expédition";
        $header[] = "Num client M3";
        $header[] = "Client";
        $header[] = "Type";
        $header[] = "Mode de paiement";
        $header[] = "Avec SAV";

        $csv = implode($this->separator, $header).self::END_OF_LINE;

        $options['date_begin'] = Tools::getValue('date_begin');
        $options['date_end'] = Tools::getValue('date_end');
        
        $options['shops'] = array();
        foreach(Tools::getValue('shops') as $id => $value)
            if($value) $options['shops'][] = $id;

        foreach(Order::findIds($options) as $id) {
            $order = new Order($id);

            $new_order = true;
            $total = 0;

            foreach($order->getDetails() as $detail) {
                
                $total += $detail->total_price_tax_excl;
                $buying_price = $detail->getTotalBuyingPrice();
                $margin = $order->total_products - $buying_price;
                $margin_rate = ($order->total_products > 0) ? Tools::getMarginRate($margin, $order->total_products) : 0;

                $data = array();
                $data[] = $order->getDate('date_add')->format('d/m/Y');
                $data[] = $order->reference;
                $data[] = $detail->product_reference;
                $data[] = $detail->product_id ? $this->findFamily($detail->product_id) : '-';
                $data[] = $detail->getSupplier() ? $detail->getSupplier()->reference." - ".$detail->getSupplier()->name : '-';
                $data[] = $order->getShop()->name;
                $data[] = $order->product_supplier_reference;
                $data[] = $detail->product_name;
                $data[] = $detail->product_quantity;
                $data[] = round($detail->total_price_tax_excl, 2);
                $data[] = round($buying_price, 2);
                $data[] = round($margin, 2);
                $data[] = round($margin_rate, 2);
                $data[] = round($total, 2);
                $data[] = (int)$new_order;
                $data[] = $order->getCustomer()->id;
                $data[] = $order->getDate('delivery_date')->format('d/m/Y');
                $data[] = $order->getCustomer()->reference;
                $data[] = $order->getCustomer()->firstname." ".$order->getCustomer()->lastname;
                $data[] = $order->getCustomer()->getType() ? $order->getCustomer()->getType()->name : '-';
                $data[] = $order->payment;
                $data[] = AfterSale::hasAfterSales($order->id) ? "oui" : "non";

                $csv .= implode($this->separator, $data).self::END_OF_LINE;
                $new_order = false;
            }
        }

        $file_name = "commandes_".date('d-m_H-i').".csv";
        header("Content-Type: application/vnd.ms-excel; name=$file_name; charset=UTF-8");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        die($csv);
    }

    /**
    * Retourne la famille associée à un produit
    * @return string
    **/
    private function findFamily($id_product) {
        return Db::getInstance()->getValue("SELECT cl.name FROM ps_product p, ps_category_lang cl WHERE p.id_category_default = cl.id_category AND p.id_product = $id_product");
    }

}