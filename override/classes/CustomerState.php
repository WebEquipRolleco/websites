<?php

/**
* Gestion statut des clients
**/
class CustomerStateCore extends ObjectModel {

	/** @var string Name **/
    public $name;

    /** @var string Color **/
    public $color;

    /** @var bool Light text **/
    public $light_text;

    /** @var bool Show customer **/
    public $show_customer;

    /** @var int Risk level **/
    public $risk_level = 0;

    /**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'customer_state',
        'primary' => 'id_customer_state',
        'fields' => array(
        	'name' => array('type' => self::TYPE_STRING, 'required' => true),
        	'color' => array('type' => self::TYPE_STRING, 'required' => true),
        	'light_text' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        	'show_customer' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'risk_level' => array('type' => self::TYPE_INT)
        )
    );

    /**
    * Retourne la liste des états clients
    **/
    public static function getCustomerStates() {

        $data = array();

        $ids = Db::getInstance()->executeS('SELECT id_customer_state FROM ps_customer_state');
        foreach($ids as $row) 
            $data[] = new CustomerState($row['id_customer_state']);

        return $data;
    }

}