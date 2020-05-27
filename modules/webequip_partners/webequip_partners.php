<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once "classes/Partner.php";
require_once "classes/PartnerRequest.php";

class Webequip_Partners extends Module {

	public function __construct() {
        $this->name = 'webequip_partners';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Partenaires";
        $this->description = "Affiche les logos des entreprises partenaires";

        parent::__construct();
    }

    public function install() {
        Partner::createTable();
        return (parent::install() && $this->registerHook('displayContentWrapperBottom') && $this->registerHook('ActionFrontControllerSetMedia'));
    }

    public function uninstall() {
        Partner::removeTable();
        return parent::uninstall();
    }

    public function getContent() {

        if(Tools::isSubmit('remove_slide')) {

            $slide = new Partner(Tools::getValue('remove_slide'));

            @unlink(_PS_MODULE_DIR_.$this->name.'/img/'.$slide->picture);
            $slide->delete();
        }

        if(Tools::getIsset('new_slide')) {

            $form = Tools::getValue('new_slide');

            $uploader = new UploaderCore('new_slide');
            $uploader->setSavePath(_PS_MODULE_DIR_.$this->name.'/img/');
            
            $files = $uploader->process();
            if(!empty($files)) {

                foreach($files as $file)
                    $file_name = $file['name'];

                $partner = new Partner();
                $partner->name = $form['name'];
                $partner->picture = $file_name;

                $partner->save();
            }
        }

        $this->context->smarty->assign('slides', Partner::findAll());
        return $this->display(__FILE__, 'config.tpl');
    }

    public function hookActionFrontControllerSetMedia($params) {
        $this->context->controller->registerJavascript('slick-js', 'modules/'.$this->name.'/views/js/slick.js');
        $this->context->controller->registerJavascript('partners-custom-js', 'modules/'.$this->name.'/views/js/custom.js');
        $this->context->controller->registerStylesheet('slick-css', 'modules/'.$this->name.'/views/css/slick.css');
    }

    public function hookDisplayContentWrapperBottom($params) {
        $this->context->smarty->assign('slides', Partner::findAll());
    	return $this->display(__FILE__, 'content.tpl');
    }

}