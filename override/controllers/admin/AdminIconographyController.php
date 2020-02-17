<?php

require_once("exports/Export.php");
require_once("imports/Import.php");

class AdminIconographyControllerCore extends AdminController {

	public function __construct() {
        
        $this->table = ProductIcon::TABLE_NAME;
        $this->className = 'ProductIcon';

        $this->bootstrap = true;
        $this->required_database = true;
        $this->allow_export = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->toolbar_btn['import'] = array(
            'href' => '#import',
            'desc' => $this->l('Import')
        );

        $this->fields_list = array(
            'id_product_icon' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'align' => 'center',
            ),
            'location' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'align' => 'center',
                'callback' => 'renderLocation'
            ),
            'position' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'align' => 'center',
                'type' => 'int'
            ),
            'active' => array(
                'title' => $this->trans('Actif', array(), 'Admin.Global'),
                'align' => 'text-center',
                'type' => 'bool',
                'active' => 'status'
            ),
        );
    }

    public function renderLocation($value) {
        return ProductIcon::getLocations()[$value];
    }
    
    public function initContent() {
    	parent::initContent();

        if(Tools::isSubmit('submitResetproduct_icon'))
            $this->processResetFilters();

    	if(Tools::getIsset('updateproduct_icon') or Tools::getIsset('addproduct_icon'))
    		$this->displayForm();
        else {
            $export = new ExportIconography();
            $this->context->smarty->assign("columns", $export->getHeader());
            $this->displayList();
        }
    }

    public function postProcess() {
        if(Tools::getIsset('exportproduct_icon')) {

            $export = new ExportIconography();
            $export->export();
        }
    }

    /**
    * Affiche la page liste
    **/
    private function displayList() {

    	// Vérifier le dossier de destination des images
    	$path = getcwd().'/../img/icons';
		if(!is_dir($path)) mkdir($path, 0777);

    	// Suppression
    	if($id = Tools::getValue('delete')) {
    		$icon = new ProductIcon($id);
    		if($icon->id) $icon->delete();
    	}

    	// Changement statut 
    	if($id = Tools::getValue('toggle')) {
    		$icon = new ProductIcon($id);
    		$icon->active = !$icon->active;
    		$icon->save();
    	}

        // Import
        if(Tools::isSubmit('import')) {
            $import = new ImportIconography();
            $import->import();

            if($import->nb_lines)
                $this->confirmations[] = "Import terminé : ".$import->nb_lines." lignes impactées";
        }
    
        $this->getList(1);
        $this->context->smarty->assign('list', $this->renderList());
    }

    /**
    * Affiche le formulaire de création / modification
    **/
    private function displayForm() {

    	$icon = new ProductIcon(Tools::getValue('id_product_icon'));

    	// Validation du formulaire
    	if($form = Tools::getValue('form')) {

    		$icon->name = $form['name'];
    		$icon->title = $form['title'];
    		$icon->url = $form['url'];
    		$icon->active = $form['active'];
    		$icon->position = $form['position'];
            $icon->location = $form['location'];
            
    		if(isset($form['height']))
    			$icon->height = $form['height'];
    		if(isset($form['width']))
    		$icon->width = $form['width'];

    		$icon->save();

    		$icon->eraseShops();

    		foreach(Tools::getValue('shops') as $id_shop => $active)
    			if($active) $icon->addShop($id_shop);
    	}

    	// Modification de l'image
    	if(isset($_FILES['picture']) and $_FILES['picture']['name']) {

    		if($icon->extension)
    			@unlink(getcwd()."/../img/icons/".$this->id.".".$icon->extension);

    		$rows = explode('.', $_FILES['picture']['name']);
    		$icon->extension = end($rows);
    		$icon->save();

    		move_uploaded_file($_FILES['picture']['tmp_name'], getcwd()."/../img/icons/".$icon->id.".".$icon->extension);
    	}

    	// Ajout produit dans une des listes
        if($id = Tools::getValue('product')) {

            // Liste blanche
            if(Tools::isSubmit('add_white_list')) {

                $ids = $icon->getWhiteList();
                $ids[] = $id;

                $icon->product_white_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }

            // Liste noire
            if(Tools::isSubmit('add_black_list')) {

                $ids = $icon->getBlackList();
                $ids[] = $id;
                
                $icon->product_black_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Ajout catégorie dans une des listes
        if($id = Tools::getValue('category')) {

            // Liste blanche
            if(Tools::isSubmit('add_white_list')) {

                $ids = $icon->getCategoryWhiteList();
                $ids[] = $id;

                $icon->category_white_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }

            // Liste noire
            if(Tools::isSubmit('add_black_list')) {

                $ids = $icon->getCategoryBlackList();
                $ids[] = $id;
                
                $icon->category_black_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Ajout fournisseur dans une des listes
        if($id = Tools::getValue('supplier')) {

            // Liste blanche
            if(Tools::isSubmit('add_white_list')) {

                $ids = $icon->getSupplierWhiteList();
                $ids[] = $id;

                $icon->supplier_white_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }

            // Liste noire
            if(Tools::isSubmit('add_black_list')) {

                $ids = $icon->getSupplierBlackList();
                $ids[] = $id;
                
                $icon->supplier_black_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression produit de la liste blanche
        if(Tools::isSubmit('remove_white_list') and $id = Tools::getValue('remove_white_list')) {

            $ids = $icon->getWhiteList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->product_white_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression produit de la liste noire
        if(Tools::isSubmit('remove_black_list') and $id = Tools::getValue('remove_black_list')) {

            $ids = $icon->getBlackList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->product_black_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression catégorie de la liste blanche
        if(Tools::isSubmit('remove_category_white_list') and $id = Tools::getValue('remove_category_white_list')) {

            $ids = $icon->getCategoryWhiteList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->category_white_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression catégorie de la liste noire
        if(Tools::isSubmit('remove_category_black_list') and $id = Tools::getValue('remove_category_black_list')) {

            $ids = $icon->getCategoryBlackList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->category_black_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression fournisseur de la liste blanche
        if(Tools::isSubmit('remove_supplier_white_list') and $id = Tools::getValue('remove_supplier_white_list')) {

            $ids = $icon->getSupplierWhiteList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->supplier_white_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression fournisseur de la liste noire
        if(Tools::isSubmit('remove_supplier_black_list') and $id = Tools::getValue('remove_supplier_black_list')) {

            $ids = $icon->getSupplierBlackList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->supplier_black_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

    	$this->context->smarty->assign('icon', $icon);
    	$this->context->smarty->assign('products', Db::getInstance()->executes("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_shop = ".$this->context->shop->id." AND name <> '' AND name IS NOT NULL"));
        $this->context->smarty->assign('categories', Category::getAllCategoriesName(null, 1));
        $this->context->smarty->assign('suppliers', Supplier::getSuppliers(1));
        $this->context->controller->addjQueryPlugin('select2');

    	$this->setTemplate('details.tpl');
    }

}