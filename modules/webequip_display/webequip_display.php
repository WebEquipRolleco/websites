<?php

if (!defined('_PS_VERSION_'))
    exit;

include "classes/Display.php";

class Webequip_Display extends Module {

	public function __construct() {
        $this->name = 'webequip_display';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Publicités";
        $this->description = "Affiche des blocs de publicités sur la boutique";

        parent::__construct();
    }

    public function install() {
        Display::createTable();
        return (parent::install() && $this->registerHook('displayHome'));
    }

    public function uninstall() {
        Display::removeTable();
        return parent::uninstall();
    }

    public function getContent() {

        if(Tools::isSubmit('remove_display')) {

            $display = new Display(Tools::getValue('remove_display'));

            @unlink(_PS_MODULE_DIR_.$this->name.'/img/'.$display->picture);
            $display->delete();
        }

        if(Tools::getIsset('display')) {

            $form = Tools::getValue('display');
            $display = new Display($form['id']);

            if($_FILES['display']['name']['file']) {

                $uploader = new UploaderCore('display');
                $uploader->setSavePath(_PS_MODULE_DIR_.$this->name.'/img/');

                $files = $uploader->process();
                if(!empty($files)) {

                    foreach($files as $file)
                        if($file['name'])
                            $display->picture = $file['name'];
                }
            }

            $display->name = $form['name'];
            $display->link = $form['link'];
            $display->position = $form['position'];
            $display->active = $form['active'];
            $display->save();

            if(isset($form['shops']) and is_array($form['shops'])) {
                $display->erazeShops();

                foreach($form['shops'] as $id_shop => $status)
                    $display->addShop($id_shop, $status);
            }
        }

        $this->context->smarty->assign('displays', Display::findAll());
        $this->context->smarty->assign('module_link', $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name);

        return $this->display(__FILE__, 'config.tpl');
    }

    public function ajaxProcessDetails() {

        $display = new Display(Tools::getValue('id'));

        $this->context->smarty->assign('display', $display);
        $this->context->smarty->assign('shops', Shop::getShops());

        die($this->display(__FILE__, 'details.tpl'));
    }

    public function hookDisplayHome($params) {
        $this->context->smarty->assign('displays', Display::find());
        return $this->display(__FILE__, 'content.tpl');
    }

}