<?php

class AdminImportExportControllerCore extends AdminController {

	const SEPARATOR = "@";
	const DELIMITER = "|";
	const END_OF_LINE = "\n";

	const TYPE_PRODUCT = "Product";

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

    	// Export
    	if(Tools::isSubmit('export')) {

    		$header[] = "ID";
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

    		$csv = implode($this->separator, $header).self::END_OF_LINE;

    		$sql = "SELECT p.id_product, (SELECT GROUP_CONCAT(id_category SEPARATOR '".$this->delimiter."') FROM ps_category_product WHERE id_product = p.id_product) AS id_categories FROM ps_product p WHERE 1";
    		if($category_ids = implode(',', Tools::getValue('categories', array())))
    			$sql .= " AND id_category_default IN ($category_ids)";
    		if($supplier_ids = implode(',', Tools::getValue('suppliers', array())))
    			$sql .= " AND id_supplier IN ($supplier_ids)";

    		foreach(Db::getInstance()->executeS($sql) as $row) {
    			$product = new Product($row['id_product'], true, 1);

    			$data = array();
    			$data[] = $product->id;
    			$data[] = "Produit";
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
				// Liste de toutes les caractéristiques

				$csv .= implode($this->separator, $data).self::END_OF_LINE;
    		}

    		header('Content-Disposition: attachment; filename="produits.csv";');
			die($csv);
    	}

    	// Import
    	if(Tools::isSubmit('import')) {
    		if($file = $_FILES['import_file']) {

    			$handle = fopen($file['tmp_name'], 'r');
    			
    			// Lignes à ignorer
    			for($x=0; $x<=Tools::getValue('skip'); $x++)
    				fgetcsv($handle, 0, $this->separator);

    			while($row = fgetcsv($handle, 0, $this->separator)) {

    				// Produit
    				if($row[1] == self::TYPE_PRODUCT) {
    					$product = new Product($row[0], true, 1);

		    			$product->reference = $row[2];
		    			$product->id_category_default = (int)$row[3];
		 				$product->name = $row[5];
		 				$product->minimal_quantity = (int)$row[6];
		 				$product->quantity = (int)$row[7];
		 				$product->low_stock_threshold = (int)$row[8];
		 				$product->active = (bool)$row[9];
		 				$product->rollcash = (float)$row[10];
						//$data[] = "Rollplus";
						$product->description_short = $row[11];
						$product->description = $row[12];
						$product->link_rewrite = $row[13];
						$product->meta_title = $row[14];
						$product->meta_description = $row[15];
						$product->meta_keywords = $row[16];
						$product->id_supplier = (int)$row[17];

						$product->save();

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

    			}

    			fclose($handle);
    		}
    	}
    }
}