<?php

class Configuration extends ConfigurationCore {

	/**
	* Configuration en fonction d'une commande (peu importe le contexte)
	**/
	public static function getForOrder($name, $order, $default = false) {
		return self::get($name, null, $order->id_shop_group, $order->id_shop, $default);
	}
	
}