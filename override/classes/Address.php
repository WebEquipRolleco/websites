<?php

class Address extends AddressCore {

	/**
	* Vérifie si l'adresse possède au moins un numéro de téléphone
	**/
	public function hasPhone() {
		return $this->phone || $this->phone_mobile;
	}

	/** 
	* Vérifie si les 2 numéros de téléphone sont renseignés
	**/
	public function hasBothPhones() {
		return $this->phone && $this->phone_mobile;
	}

}