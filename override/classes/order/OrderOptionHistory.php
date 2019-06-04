<?php

/**
* Utilisation des options dans les commandes
**/
class OrderOptionHistoryCore extends ObjectModel {

    const TABLE_NAME = 'order_option_history';
    const TABLE_PRIMARY = 'id';

    /** @var int Id order **/
	public $id_order; 
	
    /** @var string Name **/
    public $name;

    /** @var string Description **/
    public $description;

    /** @var float Value **/
	public $value; 

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'id_order' => array('type'=>self::TYPE_INT, 'validate'=>'isInt', 'required' => true),
            'name' => array('type'=>self::TYPE_STRING, 'validate'=>'isGenericName', 'required' => true),
        	'description' => array('type'=>self::TYPE_STRING),
        	'value' => array('type'=>self::TYPE_FLOAT, 'validate'=>'isFloat', 'required'=>true)
        )
    );

}