<?php

/**
* Affiche une liste de commande possédant une option de commande (paramétrable)
**/
class AdminWaitingOrdersController extends AdminController {

	const CONFIG_OPTION_ID = "CONFIG_OPTION_ID";
	const CONFIG_EXCLUDE_STATES = "PRINT_EXCLUDE_STATES";

	const SEPARATOR = ",";

	/**
	* Activer Bootstrap
	**/
	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

    /**
    * Enregistrement de la configuration
    **/
	public function postProcess() {
		
		if(Tools::getIsset(self::CONFIG_OPTION_ID))
			Configuration::updateValue(self::CONFIG_OPTION_ID, Tools::getValue(self::CONFIG_OPTION_ID));

		if(Tools::getIsset(self::CONFIG_EXCLUDE_STATES))
			Configuration::updateValue(self::CONFIG_EXCLUDE_STATES, implode(self::SEPARATOR, Tools::getValue(self::CONFIG_EXCLUDE_STATES)));
	}

	/**
	* Récupère la configuration et laliste des commandes
	**/
	public function initContent() {

		parent::initContent();

		$id_option = Configuration::get(self::CONFIG_OPTION_ID);
		$states = Configuration::get(self::CONFIG_EXCLUDE_STATES);

		$this->context->smarty->assign('states', OrderState::getOrderStates(1));
		$this->context->smarty->assign('options', OrderOption::getOrderOptions());
		$this->context->smarty->assign('orders', $this->findOrders($id_option, $states));
		$this->context->smarty->assign(self::CONFIG_OPTION_ID, $id_option);
		$this->context->smarty->assign(self::CONFIG_EXCLUDE_STATES, explode(self::SEPARATOR, $states));
	}

	/**
	* Retourne une liste de commande en fonction d'une option et d'état à exclure
	* @param int $id_option
	* @param string $exclude
	* @return array
	**/
	private function findOrders($id_option, $exclude) {

		if(!$id_option)
			return array();

		$sql = "SELECT DISTINCT(o.id_order), o.date_add FROM ps_orders o, ps_order_detail d, ps_order_option_cart c WHERE o.id_order = d.id_order AND o.id_cart = c.id_cart AND c.id_option = $id_option";
		if($exclude) $sql .= " AND o.current_state NOT IN ($exclude)";
		$sql .= " ORDER BY o.date_add DESC";

		$data = array();
		foreach(Db::getInstance()->executeS($sql) as $row)
			$data[] = new Order($row['id_order']);

		return $data;
	}

}