<?php

class ProductIconGroup extends ObjectModel {

	const TABLE_NAME = 'product_icon_group';
	const TABLE_PRIMARY = 'id_product_icon_group';

	/** @var string Name **/
	public $name;

	/** @var bool Unique **/
	public $unique;

	/**
	* @see ObjectModel::$definition
	**/
	public static $definition = array(
		'table' => self::TABLE_NAME,
		'primary' => self::TABLE_PRIMARY,
		'fields' => array(
			'name'		=> array('type' => self::TYPE_STRING),
			'unique' 	=> array('type' => self::TYPE_BOOL)
		)
	);

	/**
	* Retourne la liste des groupes
	**/
	public function find() {
		return Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_.self::TABLE_NAME);
	}

}