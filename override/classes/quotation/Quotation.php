<?php

class Quotation extends ObjectModel {

	const TABLE_NAME = 'webequip_quotations';
	const TABLE_PRIMARY = 'id';

    const STATUS_WAITING = 1;
    const STATUS_VALIDATED = 2;
    const STATUS_REFUSED = 3;
    const STATUS_OVER = 4;

    const ORIGIN_MAIL = 1;
    const ORIGIN_PHONE = 2;
    const ORIGIN_FAX = 3;
    const ORIGIN_OTHERS = 4;

    public $reference;
    public $status = self::STATUS_WAITING;
    public $id_customer;
    public $origin;
    public $email;
    public $hidden_emails;
    public $date_begin;
    public $date_add;
    public $date_end;
    public $date_recall;
    public $phone;
    public $fax;
    public $comment;
    public $details;
    public $id_employee;
    public $active = 1;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'origin' => array('type' => self::TYPE_INT),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'hidden_emails' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_begin' => array('type' => self::TYPE_DATE),
            'date_end' => array('type' => self::TYPE_DATE),
            'date_recall' => array('type' => self::TYPE_DATE),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'fax' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'comment' => array('type' => self::TYPE_STRING),
            'details' => array('type' => self::TYPE_STRING),
            'id_employee' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL)
        ),
    );

    /**
    * Récupère un devis depuis une référence
    **/
    public static function findByReference($reference) {

        $id = Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE reference = '$reference'");
        return new Quotation($id);
    }

    /**
    * Retourne la liste des produits du devis
    **/
    public function getProducts() {

        $data = array();
        $rows = Db::getInstance()->executeS("SELECT ".QuotationLine::TABLE_PRIMARY." FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." WHERE id_quotation = ".$this->id);
        foreach($rows as $row) {
            $data[] = new QuotationLine($row[QuotationLine::TABLE_PRIMARY]);
        }

        return $data;
    }

    /**
    * Retourne la liste des labels des états devis
    **/
    public static function getStates() {

        $data[self::STATUS_REFUSED] = "Refusé";
        $data[self::STATUS_WAITING] = "En attente";
        $data[self::STATUS_VALIDATED] = "Accepté";
        $data[self::STATUS_OVER] = "Terminé";

        return $data;
    }

    public function getPrice($use_tax = false) {

        $price = 0;
        foreach($this->getProducts() as $line)
            $price += $line->getPrice();

        if($use_tax)
            $price *= 1.2;

        return $price;
    }
    /**
    * Retourne le label de l'état courant du devis
    **/
    public function getStatusLabel() {

        $labels = self::getStates();
        return isset($labels[$this->status]) ? $labels[$this->status] : null;
    }

    /**
    * Retourne la class Bootstrap liée à l'état courant du devis
    **/
    public function getStatusClass() {
        if($this->status == self::STATUS_WAITING) return "primary";
        if($this->status == self::STATUS_VALIDATED) return "success";
        if($this->status == self::STATUS_REFUSED) return "danger";
        if($this->status == self::STATUS_OVER) return "default";
        return null;
    }

    /**
    * Retourne la liste des labels des origines possibles des devis
    **/
    public static function getOrigins() {

        $data[self::ORIGIN_MAIL] = "Mail";
        $data[self::ORIGIN_PHONE] = "Téléphone";
        $data[self::ORIGIN_FAX] = "Fax";
        $data[self::ORIGIN_OTHERS] = "Autre";

        return $data;
    }

    /**
    * Retourne le label de l'état courant du devis
    **/
    public function getOriginLabel() {

        $origins = self::getOrigins();
        return isset($origins[$this->origin]) ? $origins[$this->origin] : null;
    }

    /**
    * Retourne l'object client
    **/
    public function getCustomer() {
        return $this->id_customer ? new Customer($this->id_customer) : null;
    }

    /**
    * Retourne l'object employé
    **/
    public function getEmployee() {
        return $this->id_employee ? new Employee($this->id_employee) : null;
    }

    /**
    * Verifie si le devis est toujours valable
    **/
    public function isValid() {

        if(!$this->active)
            return false;

        if($this->status != self::STATUS_WAITING)
            return false;

        $date = new DateTime('now');
        $date_begin = new DateTime($this->date_begin);
        $date_end = new DateTime($this->date_end);

        return ($date >= $date_begin and $date <= $date_end);
    }

    /**
    * Retourne les emails de contacts
    **/
    public function getEmails() {

        if($this->email)
            return explode(',', $this->email);

        $customer = $this->getCustomer();
        if($customer) return array($customer->email);

        return false;
    }

    /**
    * Retourne les emails de contacts cachés
    **/
    public function getHiddenEmails() {

        if($this->hidden_emails)
            return explode(',', $this->hidden_emails);

        return false;
    }

    /**
    * Récupère une liste de devis en fonction d'options
    **/
    public static function find($options = array()) {

        $data = array();
        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE 1";

        if(isset($options['id_customer']) and $options['id_customer'])
            $sql .= " AND id_customer = ".$options['id_customer'];

        if(isset($options['id_employee']) and $options['id_employee'])
            $sql .= " AND id_employee = ".$options['id_employee'];

        if(isset($options['active'])) {
            $sql .= " AND active = ".(int)$options['active'];
            $sql .= " AND (date_begin = '0000-00-00' OR date_begin <= '".date('Y-m-d')."')";
            $sql .= " AND (date_end = '0000-00-00' OR date_end >= '".date('Y-m-d')."')";
        }

        if(isset($options['reference']) and $options['reference'])
            $sql .= " AND reference LIKE '%".$options['reference']."%'";

        if(isset($options['date_add']) and $options['date_add'])
            $sql .= " AND date_add LIKE '".$options['date_add']."%'";

        if(isset($options['expired']))
            $sql .= " AND date_end < '".date('Y-m-d')."'";

        if(isset($options['date_recall']))
            $sql .= " AND date_recall = '".$options['date_recall']."'";

        if(isset($options['states']) and !empty($options['states']))
            $sql .= " AND status IN (".implode(',', $options['states']).")";

        $sql .= " ORDER BY date_add DESC ";

        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = new Quotation($row['id']);

        return $data;
    }

}