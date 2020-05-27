<?php

if (!defined('_PS_VERSION_'))
    exit;

include "classes/Modal.php";

class Webequip_Modal extends Module {

    private $link;

	public function __construct() {
        $this->name = 'webequip_modal';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Modals";
        $this->description = "Gestion des fenêtres d'informations envoyées aux clients";

        parent::__construct();
        $this->link = new Link();
    }

    public function install() {
        Modal::createTable();
        return (parent::install() && $this->registerHook('displayBeforeBodyClosingTag'));
    }

    public function uninstall() {
        Modal::removeTable();
        return parent::uninstall();
    }

    public function hookDisplayBeforeBodyClosingTag($params) {

        $modals = Modal::find();
        foreach($modals as $modal)
            $this->updateCookie($modal);
        
        $this->context->smarty->assign('modals', $modals);
        return $this->display(__FILE__, 'footer.tpl');
    }

    public function getContent() {
        $this->context->controller->addjQueryPlugin('select2');
        
        $id = Tools::getValue('copy_modal');
        if($id) {

            $modal = new Modal($id);
            $modal->id = null;
            $modal->active = false;
            $modal->save();
        }

        $id = Tools::getValue('delete_modal');
        if($id) {

            $modal = new Modal($id);
            $modal->delete();
        }

        $form = Tools::getValue('modal');
        if($form) {
            
            $modal = new Modal($form['id']);

            $modal->icon = $form['icon'];
            $modal->title = $form['title'];
            $modal->subtitle = $form['subtitle'];
            $modal->content = $form['content'];
            $modal->transition_in = $form['transition_in'];
            $modal->transition_out = $form['transition_out'];
            $modal->auto_open = $form['auto_open'];
            $modal->auto_close = $form['auto_close'];
            $modal->fullscreen = $form['fullscreen'];
            $modal->close_button = $form['close_button'];
            $modal->close_escape = $form['close_escape'];
            $modal->close_overlay = $form['close_overlay'];
            $modal->header_color = $form['header_color'];
            $modal->overlay = $form['overlay'];
            $modal->width = $form['width'];
            $modal->top = $form['top'];
            $modal->bottom = $form['bottom'];
            $modal->date_begin = $form['date_begin'];
            $modal->date_end = $form['date_end'];
            $modal->active = $form['active'];
            $modal->browsing = $form['browsing'];
            $modal->expiration = $form['expiration'];
            $modal->validation = $form['validation'];

            $modal->display_for_customers = $form['display_for_customers'];
            $modal->display_for_guests = $form['display_for_guests'];

            $modal->allow_pages = $form['allow_pages'];
            $modal->disable_pages = $form['disable_pages'];
            
            $modal->allow_customers = implode(",", $form['allow_customers'] ?? array());
            $modal->disable_customers = implode(",", $form['disable_customers'] ?? array());
            $modal->allow_groups = implode(",", $form['allow_groups'] ?? array());
            $modal->disable_groups = implode(",", $form['disable_groups'] ?? array());

            $modal->save();
        }

        $this->context->smarty->assign('modals', Modal::findAll());
        $this->context->smarty->assign('module_link', $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name);

        return $this->display(__FILE__, 'config.tpl');
    }

    public function ajaxProcessDetails() {

        $modal = new Modal(Tools::getValue('id'));

        $this->context->smarty->assign('modal', $modal);
        $this->context->smarty->assign('animations_in', Modal::getAnimationsIn());
        $this->context->smarty->assign('animations_out', Modal::getAnimationsOut());
        $this->context->smarty->assign('customers', Customer::getCustomers());
        $this->context->smarty->assign('groups', Group::getGroups(1));

        die($this->display(__FILE__, 'details.tpl'));
    }

    public function ajaxPRocessUpdateCookie() {

        $modal = new Modal(Tools::getValue('id'));
        if($modal->id)
            $this->updateCookie($modal, true);
    }

    public function updateCookie($modal, $ajax = false) {

        if($modal->validation and !$ajax)
            return false;

        if($modal->expiration < 0)
            setcookie(Modal::TABLE_NAME.'['.$modal->id.']', $modal->id,  time() * 2, "/");
        if($modal->expiration > 0)
            setcookie(Modal::TABLE_NAME.'['.$modal->id.']', $modal->id, time() + $modal->expiration, "/");

        return true;
    }

}