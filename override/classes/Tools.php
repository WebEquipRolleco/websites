<?php

class Tools extends ToolsCore {

	const BROWSE_INDEX = "BROWSE_TIMER";
	const EMPTY_DATE = "0000-00-00 00:00:00";

	/**
	* Vérifie si un date est vide
	* @return bool
	**/
	public static function isEmptyDate($value) {
		return (bool)(!$value or $value == self::EMPTY_DATE);
	}
	
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

	/**
	* Calcule un taux entre 2 valeurs
	**/
	public static function getRate($nb_now, $nb_then) {

		if($nb_now == 0)
			return 0;

		if($nb_then == 0 || $nb_now == $nb_then) 
			return 100;

		if($nb_now > $nb_then)
			return ($nb_now - $nb_then) / $nb_then * 100;
		else
			return -(($nb_then - $nb_now) / $nb_then * 100);
	}

	/**
	* Calcule un taux de marge entre 2 valeurs
	**/
	public static function getMarginRate($nb, $total, $precision = 1) {

		if($nb and $total)
			return round(($nb  / $total) * 100, $precision);

		return 0;
	}

	/**
	* Efface un dossier de façon récursive
	**/
	public static function erazeDirectory($path) {

		foreach(glob($path.'/*.*') as $file)
			unlink($file);

		return rmdir($path);
	}

}