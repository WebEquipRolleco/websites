<?php

class Webequip_PartnersRequestModuleFrontController extends ModuleFrontController {

	/**
    * @see FrontController::initContent()
    **/
	public function initContent() {
		parent::initContent();

		$data['count'] = 2;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('title'=>'Devenir fournisseur');

        $form = Tools::getValue('partner');
        if($form) {

        	$exists = PartnerRequest::findOneByCompany($form['company']);
        	if(!$exists) {

	        	$request = new PartnerRequest();

	        	$request->firstname = $form['firstname'];
	        	$request->lastname = $form['lastname'];
	        	$request->company = $form['company'];
	        	$request->phone = $form['phone'];
	        	$request->email = $form['email'];
	        	$request->content = $form['content'];

	        	$request->save();
	        	$this->context->smarty->assign('validation', true);
	        }
	        else{

	        	$this->context->smarty->assign('exists', true);	
	        	$this->context->smarty->assign('request', $exists);
	        }
        }

		$this->context->smarty->assign('breadcrumb', $data);
        $this->setTemplate('module:webequip_partners/views/templates/hook/form.tpl');
	}
}