<?php

if (!defined('_PS_VERSION_'))
    exit;

include "classes/Review.php";

class Webequip_reviews extends Module {

	public function __construct() {
        $this->name = 'webequip_reviews';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Avis Clients";
        $this->description = "Permet aux clients de laisser un avis sur les produits";

        parent::__construct();
    }

    public function install() {
    	Review::createTable();
        return (parent::install() and $this->registerHook('displayProductAfterTitle') and $this->registerHook('displayFooterProduct') and $this->registerHook('displayCustomerAccount'));
    }

    public function hookDisplayProductAfterTitle($params) {

        $this->context->smarty->assign('hide_link', (isset($params['hide_link']) and $params['hide_link']));
        $this->context->smarty->assign('nb', Review::getNbRating($params['product']['id_product']));
    	$this->context->smarty->assign('rating', Review::getAvgRating($params['product']['id_product']));
    	return $this->display(__FILE__, 'product_header.tpl');
    }
    
    public function hookDisplayFooterProduct($params) {

    	$this->context->smarty->assign('product_name', $params['product']['name']);
    	$this->context->smarty->assign('nb', Review::getNbRating($params['product']['id_product']));
    	$this->context->smarty->assign('rating', Review::getAvgRating($params['product']['id_product']));
    	$this->context->smarty->assign('reviews', Review::getReviews($params['product']['id_product']));
    	return $this->display(__FILE__, 'reviews.tpl');
    }

    public function hookDisplayCustomerAccount($params) {

        $link = new Link();
        $this->context->smarty->assign('url', $link->getModuleLink($this->name, 'account'));
        $this->context->smarty->assign('icon', 'star');
        $this->context->smarty->assign('text', "Mes avis");

        return $this->fetch(_PS_THEME_DIR_."templates/customer/_partials/account-link.tpl");
    }

    /**
    * Configuration du module
    **/
    public function getContent() {

        if($form = Tools::getValue('review')) {
            $review = new Review(Tools::getValue('id_review'));

            $review->name = $form['name'];
            $review->comment = $form['comment'];
            $review->rating = $form['rating'];
            $review->active = $form['active'];

            $review->save();
        }

        if(Tools::getIsset('updateReview'))
            return $this->renderForm();
        else
            return $this->renderList(); 
    }

    /**
    * Affiche la liste des avis
    **/
    public function renderList() {

        $fields_list = array(
            Review::TABLE_PRIMARY => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
            ),
            'name' => array(
                'title' => $this->trans('Produit', array(), 'Admin.Global'),
            ),
            'comment' => array(
                'title' => $this->trans('Commentaire', array(), 'Admin.Global'),
            ),
            'comment' => array(
                'title' => $this->trans('Commentaire', array(), 'Admin.Global'),
            ),
            'customer' => array(
                'title' => $this->trans('Client', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'rating' => array(
                'title' => $this->trans('Note', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'active' => array(
                'title' => $this->trans('Actif', array(), 'Admin.Global'),
                'align' => 'text-center',
                'type' => 'bool',
                'active' => 'status'
            ),

        );

        $helper_list = new HelperList();
        $helper_list->module = $this;
        $helper_list->title = $this->trans('Avis clients', array(), 'Modules.webequip_reviews.Admin');
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->show_toolbar = true;
        $helper_list->simple_header = false;
        $helper_list->identifier = 'id';
        $helper_list->table = 'Review';
        $helper_list->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
        $helper_list->token = Tools::getAdminTokenLite('AdminModules');
        $helper_list->actions = array('edit');

        /* Retrieve list data */
        $reviews = Review::findList();
        $helper_list->listTotal = count($reviews);

        return $helper_list->generateList($reviews, $fields_list);
    }

    public function renderForm() {

        $this->context->smarty->assign('action', $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name);
        $this->context->smarty->assign('review', new Review(Tools::getValue('id')));
        return $this->display(__FILE__, 'views/templates/admin/form.tpl');
    }

}