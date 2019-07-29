<?php 

class AfterSaleMessage extends ObjectModel {

	const TABLE_NAME = 'after_sale_message';
	const TABLE_PRIMARY = 'id_after_sale_message';

	public $id_after_sale;
	public $id_customer;
	public $id_employee;
	public $message;
	public $display = true;
	public $new = true;
    public $date_add;

	// Variables temporaires
	private $sender;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'id_after_sale' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'id_employee' => array('type' => self::TYPE_INT),
            'message' => array('type' => self::TYPE_STRING),
            'display' => array('type' => self::TYPE_BOOL),
            'new' => array('type' => self::TYPE_BOOL),
            'date_add' => array('type' => self::TYPE_DATE)
        )
    );

    /**
    * Retourne le client (expéditeur du message) 
    * @return null|Customer
    **/
    public function getCustomer() {

    	if(!$this->sender and $this->id_customer)
    		$this->sender = new customer($this->id_customer);

    	return $this->sender;
    }

    /**
    * Retourne l'employée (expéditeur du message)
    * @return null|Employee
    **/
    public function getEmployee() {

    	if(!$this->sender and $this->id_employee)
    		$this->sender = new Employee($this->id_employee);

    	return $this->sender;
    }

    /**
    * Retourne l'expéditeur du message
    * @return Customer|Employee
    **/
    public function getSender() {
        if($this->getCustomer())
            return $this->getCustomer();
        else
            return $this->getEmployee();
    }

    /**
    * Retourne la liste des messages d'un SAV
    **/
    public static function findByIdAfterSale($id_after_sale, $only_display = false) {

    	$sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_after_sale = $id_after_sale";
    	if($only_display) $sql .= " AND display = 1";

    	$data = array();
    	foreach(Db::getInstance()->executeS($sql) as $row)
    		$data[] = new self($row[self::TABLE_PRIMARY]);

    	return $data;
    }

}