<?php

class Ps_CustomerSignInOverride extends Ps_CustomerSignIn {

	public function hookDisplayNav1($params) {

		$link = new Link();
		$this->context->smarty->assign('logged', $this->context->customer->isLogged());
		$this->context->smarty->assign('logout_url', $link->getPageLink('index', true, null, 'mylogout'));

		return $this->display(__FILE__, 'nav1.tpl');
	}
}