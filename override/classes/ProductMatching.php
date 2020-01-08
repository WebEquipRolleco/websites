<?php

/**
* Classe utilisée pour les transfert de données entre l'ancienne BDD et la nouvelle BDD
* afin de garder une trace entre les anciens ID produits et leurs nouvelles valeurs
**/
class ProductMatching extends ObjectModel {

	const TABLE_NAME = "product_matching";
	const TABLE_PRIMARY = "id_product_matching";

	/** @var int id_product_matching **/
	public $id_product_matching;

	/** @var int id_product **/
	public $id_product;

	/** @var int id_combination **/
	public $id_combination;

	/**
	* @see ObjectModel::$definition
	**/
	public static $definition = array(
		'table' => self::TABLE_NAME,
		'primary' => self::TABLE_PRIMARY,
		'fields' => array(
			'id_product_matching'	=> array('type' => self::TYPE_INT),
			'id_product'			=> array('type' => self::TYPE_INT),
			'id_combination'		=> array('type' => self::TYPE_INT)
		)
	);

	/**
	* Ajoute une ligne de concordance produit
	* @param int $old_id_product
	* @param int $new_id_product
	* @param int $new_id_combination
	**/
	public static function record($old_id_product, $new_id_product, $new_id_combination = null) {

		$matching = new self();

		$matching->id_product_matching = $old_id_product;
		$matching->id_product = $new_id_product;
		if($new_id_combination) $matching->id_combination = $new_id_combination;
		
		$matching->save();
	}

	/**
	* Efface le contenu complet de la table
	* UTILISATION : import des données avec effacement des anciennes données
	**/
	public static function erazeContent() {
		Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME);
	}

}