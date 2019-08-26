<?php

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

    public function initContent() {
    	parent::initContent();

        if(Tools::isSubmit('submitResetproduct_icon'))
            $this->processResetFilters();

    	if(Tools::getIsset('updateproduct_icon') or Tools::getIsset('addproduct_icon'))
    		$this->displayForm();
        else
    	   $this->displayList();
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
            if(isset($_FILES['file'])) {

                $zip = new ZipArchive;
                if($zip->open($_FILES['file']['tmp_name'])) {

                    $dir = _PS_ROOT_DIR_.'/upload/'.uniqid()."/";
                    mkdir($dir);

                    $zip->extractTo($dir);
                    foreach(glob($dir.'*.csv') as $path_csv) {

                        $handle = fopen($path_csv, 'r');

                        if(Tools::getValue('skip'))
                            fgetcsv($handle, 0, ";");

                        while($row = fgetcsv($handle, 0, ";")) {

                            $icon = new ProductIcon($row[0]);
                            $icon->name = $row[1];
                            $icon->title = $row[2];
                            $icon->url = $row[3];
                            $icon->height = $row[5];
                            $icon->width = $row[6];
                            $icon->white_list = $row[7];
                            $icon->black_list = $row[8];
                            $icon->position = $row[9];
                            $icon->active = (bool)$row[10];
                            $icon->save();

                            // Gestion image
                            if($row[4] and is_file($dir.$row[4])) {

                                $icon->extension = pathinfo($dir.$row[4], PATHINFO_EXTENSION);
                                $icon->save();

                                rename($dir.$row[4], _PS_ROOT_DIR_._PS_IMG_."icons/".$icon->id.".".$icon->extension);
                            }

                            // Gestion boutiques
                            if($row[11])
                                $icon->eraseShops();

                            foreach(explode(',', $row[11]) as $id_shop)
                                if(!$icon->hasShop($id_shop, false))
                                    $icon->addShop($id_shop);
                        }

                        fclose($handle);
                    }
                    Tools::erazeDirectory($dir);
                }  
            }
            
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

                $icon->white_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }

            // Liste noire
            if(Tools::isSubmit('add_black_list')) {

                $ids = $icon->getBlackList();
                $ids[] = $id;
                
                $icon->black_list = implode(ProductIcon::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression produit de la liste blanche
        if(Tools::isSubmit('remove_white_list') and $id = Tools::getValue('remove_white_list')) {

            $ids = $icon->getWhiteList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->white_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

        // Suppression produit de la liste noire
        if(Tools::isSubmit('remove_black_list') and $id = Tools::getValue('remove_black_list')) {

            $ids = $icon->getBlackList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $icon->black_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $icon->save();
            }
        }

    	$this->context->smarty->assign('icon', $icon);
    	$this->context->smarty->assign('products', Product::getSimpleProducts(1));

    	$this->setTemplate('details.tpl');
    }

}