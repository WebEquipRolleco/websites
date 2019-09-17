<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once "classes/Reassurance.php";

class Webequip_Reassurance extends Module {

	public function __construct() {
        $this->name = 'webequip_reassurance';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Réassurance";
        $this->description = "Gestions de l'affichage des garanties clients";

        parent::__construct();
    }

    public function install() {
        Reassurance::createTable();
        return (parent::install() and $this->registerHook('displayWrapperTop') and $this->registerHook('displayReassurance'));
    }

    public function uninstall() {
        Reassurance::removeTable();
        parent::uninstall();
    }

    public function getContent() {

        $form = Tools::getValue('reassurance');
        if($form) {

            $reassurance = new Reassurance($form['id']);
            
            $reassurance->name = $form['name'];
            $reassurance->icon = $form['icon'];
            $reassurance->text = $form['text'];
            $reassurance->link = $form['link'];
            $reassurance->location = $form['location'];
            $reassurance->position = $form['position'];
            $reassurance->active = $form['active'];
            
            $reassurance->save();

            if(isset($form['shops']) and is_array($form['shops'])) {
                $reassurance->erazeShops();

                foreach($form['shops'] as $id_shop => $status)
                    $reassurance->addShop($id_shop, $status);
            }
        }

        $this->context->smarty->assign('shops', Shop::getShops());
        $this->context->smarty->assign('locations', Reassurance::getLocations());
        $this->context->smarty->assign('reassurances', Reassurance::findAll());
        $this->context->smarty->assign('module_link', $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name);
        
        return $this->display(__FILE__, 'config.tpl');
    }

    public function ajaxProcessDetails() {

        $reassurance = new Reassurance(Tools::getValue('id'));

        $this->context->smarty->assign('reassurance', $reassurance);
        $this->context->smarty->assign('shops', Shop::getShops());
        $this->context->smarty->assign('locations', Reassurance::getLocations());

        die($this->display(__FILE__, 'details.tpl'));
    }

    public function hookDisplayWrapperTop($params) {

        $this->context->smarty->assign('reassurances', Reassurance::findByPosition(Reassurance::POSITION_TOP, $this->context->shop->id));
    	return $this->display(__FILE__, 'header.tpl');
    }

    public function hookDisplayNav1($params) {

        $this->context->smarty->assign('reassurances', Reassurance::findByPosition(Reassurance::POSITION_TOP, $this->context->shop->id));
        return $this->display(__FILE__, 'header.tpl');
    }

    public function hookDisplayReassurance($params) {

        $this->context->smarty->assign('reassurances', Reassurance::findByPosition(Reassurance::POSITION_BOTTOM, $this->context->shop->id));
        return $this->display(__FILE__, 'content.tpl');
    }

    public function hookDisplayFooterProduct($params) {

        $this->context->smarty->assign('reassurances', Reassurance::findByPosition(Reassurance::POSITION_BOTTOM, $this->context->shop->id));
        return $this->display(__FILE__, 'content.tpl');
    }
}