<?php 

class ObjectModel extends ObjectModelCore {

	/**
	* Permet le transfert de donnée en sauvegardant l'ID original
	* UTILISATION : Module de transfert de données Web-equip
	**/
	public function record($update = false) {

		if($update)
		    return $this->save();

		$this->force_id = true;
		$this->add();
	}
	
}