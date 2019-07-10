<?php

class AdminOrderOptionsControllerCore extends AdminController {

	public function __construct() {

        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'order_option';
        $this->className = 'OrderOption';
        $this->primary = 'id';
        
        AdminController::__construct();

        $this->fields_list = array(
            'id' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            )
        );
    }

	public function initContent() {
        parent::initContent();

        if(Tools::getIsset('details'))
            $this->displayForm();
        else
            $this->displayList();
    }

    /**
    * Affiche la liste des options de commande
    **/
    public function displayList() {

        // Changer le statut d'une option
        if($id = Tools::getValue('toggle_option')) {
            $option = new OrderOption($id);
            if($option->id) {
                $option->active = !$option->active;
                $option->save();
            }
        }
        
        // Suppression d'une option
        if($id = Tools::getValue('remove_option')) {
            $option = new OrderOption($id);
            if($option->id) $option->delete();
        }

        $this->context->smarty->assign('options', OrderOption::getOrderOptions(false));
    }

    /**
    * Affiche le formulaire de création / modification des options de commande
    **/
    public function displayForm() {

        $option = new OrderOption(Tools::getValue('id'));

        // Enregistrement du formulaire
        if(Tools::isSubmit('save') and $form = Tools::getValue('option')) {

            $option->name = $form['name'];
            $option->description = $form['description'];
            $option->warning = $form['warning'];
            $option->type = $form['type'];
            $option->value = $form['value'];
            $option->active = $form['active'];

            $option->save();
        }

        // Ajout produit dans une des listes
        if($id = Tools::getValue('product')) {

            // Liste blanche
            if(Tools::isSubmit('add_white_list')) {

                $ids = $option->getWhiteList();
                $ids[] = $id;

                $option->white_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $option->save();
            }

            // Liste noire
            if(Tools::isSubmit('add_black_list')) {

                $ids = $option->getBlackList();
                $ids[] = $id;
                
                $option->black_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $option->save();
            }
        }

        // Suppression produit de la liste blanche
        if(Tools::isSubmit('remove_white_list') and $id = Tools::getValue('remove_white_list')) {

            $ids = $option->getWhiteList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $option->white_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $option->save();
            }
        }

        // Suppression produit de la liste noire
        if(Tools::isSubmit('remove_black_list') and $id = Tools::getValue('remove_black_list')) {

            $ids = $option->getBlackList();
            $key = array_search($id, $ids);
            if($key !== false) {

                unset($ids[$key]);
                $option->black_list = implode(OrderOption::DELIMITER, array_filter(array_unique($ids)));
                $option->save();
            }
        }

        // Purge des paniers
        if(Tools::isSubmit('purge'))
            OrderOptionCart::purge($option->id);
        
        $this->context->smarty->assign('option', $option);
        $this->context->smarty->assign('products', Product::getSimpleProducts(1));
        $this->setTemplate("details.tpl");
    }



}