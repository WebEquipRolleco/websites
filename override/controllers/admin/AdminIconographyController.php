<?php

class AdminIconographyControllerCore extends AdminController {

	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent() {
    	parent::initContent();

    	if(Tools::getIsset('details'))
    		$this->displayForm();

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

    	$this->context->smarty->assign('icons', ProductIcon::getList(false));
    }

    /**
    * Affiche le formulaire de création / modification
    **/
    private function displayForm() {

    	$icon = new ProductIcon(Tools::getValue('id'));

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