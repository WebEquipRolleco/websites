<?php

class AdminNewsletterControllerCore extends AdminController {

	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent() {
    	
    	parent::initContent();
    	$this->createRequirements();

        if($row = Group::searchByName(Newsletter::GROUP_NAME)) {
            $group = new Group($row['id_group'], 1);
            $this->context->smarty->assign('group', $group);
        }

        if($id = CartRule::getIdByCode(strtoupper(Newsletter::GROUP_NAME))) {
            $reduction = new CartRule($id, 1);
            $this->context->smarty->assign('reduction', $reduction);
        }

    }

    /**
    * Installe les prérequis pour le fonctionnement de la réduction
    **/
    public function createRequirements() {

        $groups = Group::getGroups(1);
        foreach($groups as $row) {

            if($row['name'] == Newsletter::GROUP_NAME) {
                $group = new Group($row['id_group'], 1);
                break;
            }
        }

        if(!isset($group)) {
            $group = new Group();
            $group->name[1] = Newsletter::GROUP_NAME;
            $group->price_display_method = 1;
            $group->save();
        }

        if($group->id) {

            if(CartRule::cartRuleExists(strtoupper(Newsletter::GROUP_NAME)))
                return true;

            $reduction = new CartRule();
            $reduction->name[1] = Newsletter::GROUP_NAME;
            $reduction->date_from = date('Y-m-d H:i:s');
            $reduction->date_to = "2099-12-31 23:59:59";
            $reduction->description = "Pour vous remercier de votre inscription à la newsletter, bénéficiez de 5% sur votre commande en utilisant le code promo suivant";
            $reduction->partial_use = 0;
            $reduction->code = strtoupper(Newsletter::GROUP_NAME);
            $reduction->group_restriction = true;
            $reduction->reduction_percent = 5;
            $reduction->save();

            if($reduction->id)
                return Db::getInstance()->execute('INSERT INTO ps_cart_rule_group VALUES('.$reduction->id.', '.$group->id.')');
        }

        return false;
    }
}