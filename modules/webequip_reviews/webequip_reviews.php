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
        return (parent::install() && $this->registerHook('displayProductAfterTitle') && $this->registerHook('displayFooterProduct'));
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

}