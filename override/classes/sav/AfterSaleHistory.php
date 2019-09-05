<?php

class AfterSaleHistory extends ObjectModel {

	const TABLE_NAME = 'after_sale_history';
	const TABLE_PRIMARY = 'id_after_sale_history';

	public $id_after_sale;
	public $name;
	public $id_employee;
	public $date_add;

	// Variables temporaires
	private $employee;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'id_after_sale' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING),
            'id_employee' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE)
        )
    );

	/**
    * Retourne l'employée
    * @return null|Employee
    **/
    public function getEmployee() {

    	if(!$this->employee and $this->id_employee)
    		$this->employee = new Employee($this->id_employee);

    	return $this->employee;
    }

    /**
    * Retourne la liste des historiques d'un SAV
    * @param int $id_after_sale
    * @return array
    **/
    public static function find($id_after_sale) {

    	$data = array();
    	foreach(Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_after_sale = $id_after_sale") as $row)
    		$data[] = new self($row[self::TABLE_PRIMARY]);

    	return $data;
    }

}