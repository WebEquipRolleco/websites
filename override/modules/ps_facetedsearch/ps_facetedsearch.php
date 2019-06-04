<?php

class Ps_FacetedsearchOverride extends Ps_Facetedsearch {

	public function renderWidget($hookName, array $configuration) {

		if(Category::hasChildren(Tools::getValue('id_category'), 1))
			return false;

        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch('module:ps_facetedsearch/ps_facetedsearch.tpl');
    }
}