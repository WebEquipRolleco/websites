<?php

class Tools extends ToolsCore {

	const BROWSE_INDEX = "BROWSE_TIMER";

	/**
	* Retourne le temps de navigation d'un utilisateur
	**/
	public static function getBrowseTime() {

		if(!self::load(self::BROWSE_INDEX))
			self::save(self::BROWSE_INDEX, time());

		return round((time() - self::load(self::BROWSE_INDEX)) / 60);
	}

	/**
	* Enregistre une variable en session
	**/
	public static function save($name, $value) {
		$_SESSION[$name] = $value;
	}

	/**
	* Retourne une valeur enregistrée en session
	**/
	public static function load($name) {
		return $_SESSION[$name] ?? false;
	}

}