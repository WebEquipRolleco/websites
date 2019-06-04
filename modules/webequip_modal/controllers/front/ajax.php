<?php

class Webequip_modalAjaxModuleFrontController extends ModuleFrontController {

	public function displayAjax() {

		$action = Tools::getValue('action');
		$modal = new Modal(Tools::getValue('id'));

		if($action == 'updateCookie')
			$this->module->updateCookie($modal, true);

		die('1');
	}

}