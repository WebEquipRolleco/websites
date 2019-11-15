<?php

class Configuration extends ConfigurationCore {

	/**
	* Configuration en fonction d'une commande (peu importe le contexte)
	**/
	public static function getForOrder($name, $order, $default = false) {
		return self::get($name, null, null, $order->id_shop, $default);
	}
	
	/**
	* Configuration en fonction d'une boutique (peu importe le contexte)
	**/
	public static function getForShop($name, $shop, $default = false) {
		return self::get($name, null, null, null, $shop->id, $default);
	}
	
}