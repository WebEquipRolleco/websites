<?php

/**
* Détermine une règle de changement de statut automatique
**/
class OrderStateRule extends ObjectModel {

    const TABLE_NAME = 'order_state_rule';
    const TABLE_PRIMARY = 'id_order_state_rule';

    /** @var string Name **/
    public $name;

    /** @var string Description **/
    public $description;

    /** @var string Description **/
    public $ids; 

    /** @var int Target id **/
    public $target_id;

    /** @var bool Active **/
	public $active = true;

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'name' => array('type'=>self::TYPE_STRING, 'validate'=>'isGenericName', 'required' => true),
        	'description' => array('type'=>self::TYPE_STRING),
        	'ids' => array('type'=>self::TYPE_STRING),
            'target_id' => array('type'=>self::TYPE_INT),
        	'active' => array('type'=>self::TYPE_BOOL, 'validate'=>'isBool')
        )
    );

    /**
    * Override : chargement des IDS
    **/
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null) {

        parent::__construct($id, $id_lang, $id_shop, $translator);

        if(is_string($this->ids))
            $this->ids = explode(',', $this->ids);
    }

    /**
    * Overrider : enregistrement des IDS
    **/
    public function save($null_values = false, $auto_date = true) {
        
        $this->formatIds();
        return parent::save($null_values, $auto_date);
    }

    /**
    * Overrider : enregistrement des IDS
    **/
    public function update($null_values = false) {
        
        $this->formatIds();
        return parent::update($null_values);
    }

    /**
    * Formate les IDS états intermédiaires
    **/
    public function formatIds() {
        if(is_array($this->ids))
            $this->ids = implode(',', $this->ids);
    }

    /**
    * Retourne les règles actives
    **/
    public static function getActiveRules() {

    	$data = array();
    	foreach(Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE active = 1") as $row)
    		$data[] = new self($row[self::TABLE_PRIMARY]);

    	return $data;
    }

}