<?php

class AfterSale extends ObjectModel {

	const TABLE_NAME = 'after_sale';
	const TABLE_PRIMARY = 'id_after_sale';

    const DELIMITER = ",";

    const STATUS_WAITING = 1;
    const STATUS_ONGOING = 2;
    const STATUS_CLOSED = 3;

	public $id;
    public $reference;
    public $email;
    public $status = 1;
    public $condition;
    public $id_customer;
    public $id_order;
    public $ids_detail;
    public $date_add;
    public $date_upd;

    // Variables temporaires
    private $customer;
    private $order;
    private $details = array();

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING),
            'email' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),
            'condition' => array('type' => self::TYPE_STRING),
            'id_customer' => array('type' => self::TYPE_INT),
            'id_order' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE)
        )
    );

    /**
    * Retourne le client associé au SAV
    * @return Customer
    **/
    public function getCustomer() {

        if(!$this->customer)
            $this->customer = new Customer($this->id_customer);

        return $this->customer;
    }

    /**
    * Retourne la commande associée au SAV
    * @return Order
    **/
    public function getOrder() {

        if(!$this->order)
            $this->order = new Order($this->id_order);

        return $this->order;
    }

    /**
    * Retourne la liste des status
    * @return array
    **/
    public static function getStatuses() {

        $data[self::STATUS_WAITING] = "En attente de traitement";
        $data[self::STATUS_ONGOING] = "En cours de traitement";
        $data[self::STATUS_CLOSED] = "Clôturé";

        return $data;
    }

    /**
    * Retourne le STATUS actuel de la commande
    * @param bool fixed
    * @return string
    **/
    public function getStatusLabel($fixed = false) {

        if($this->condition)
            return $this->condition;

        return self::getStatuses()[$this->status];
    }

    /**
    * Retourne la class associée au STATUS
    * @return string
    **/
    public function getStatusClass() {

        $data[self::STATUS_WAITING] = "info";
        $data[self::STATUS_ONGOING] = "success";
        $data[self::STATUS_CLOSED] = "default";

        return $data[$this->status];
    }

    /**
    * Fonction de recherche générique en fonction d'une liste d'options
    * @param array $options
    * @return array
    **/
    public static function find($options = array()) {

        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE 1";

        if(isset($options['id_customer']))
            $sql .= " AND id_customer = ".$options['id_customer'];

        if(isset($options['id_order']))
            $sql .= " AND id_order = ".$options['id_order'];

        $data = array();
        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = new self($row[self::TABLE_PRIMARY]);

        return $data;
    }

    /**
    * Retourne la liste des SAV d'un client
    * @param int $id_customer
    * @return array
    **/
    public static function findByCustomer($id_customer) {
        return self::find(array('id_customer' => $id_customer));
    }

    /**
    * Retourne la liste des SAV d'une commande
    * @param int $id_order
    * @return array
    **/
    public static function findByOrder($id_order) {
        return self::find(array('id_order' => $id_order));
    }

    /**
    * Génère une référence en fonction d'un numéro de commande
    * @param mixed $number
    * @param string|null $prefix
    **/
    public function generateReference($number = null, $prefix = "SAV-") {

        if(!$number)
            $number = $this->getOrder()->reference;

        $reference = $prefix.$number;
        if(self::findIdByReference($reference)) {

            $nb = 2;
            do {

                $reference = $prefix.$number."-$nb";
                $nb++;

            } while(self::findIdByReference($reference));
        }

        $this->reference = $reference;
    }

    /**
    * Retourne un SAV en fonction de sa référence
    * @param mixed $reference
    * @return AfterSale
    **/
    public static function findByReference($reference) {
        return new AfterSale(self::findIdByReference($reference));
    }

    /**
    * Retourne une ID en fonction d'une référence
    * @param string $reference
    * @return int
    **/
    private function findIdByReference($reference) {
        return Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE reference = '$reference'");
    }

    /**
    * Retourne la liste des messages associés au SAV
    **/
    public function getMessages($display_only = false) {
        return AfterSaleMessage::findByIdAfterSale($this->id, $display_only);
    }

    /**
    * Retourne la liste des produits concernés par le SAV
    * @return array
    **/
    public function getProductDetails() {

        if(empty($this->details) and $this->ids_detail) {

            foreach(explode(self::DELIMITER, $this->ids_detail) as $id)
                $this->details[] = new OrderDetail($id);
        }

        return $this->details;
    }

    /**
    * Vérifie si le SAV a un nouveau messages pour le client
    * @return bool
    **/
    public function hasNewMessageForCustomer() {

        foreach($this->getMessages(true) as $message)
            if($message->id_employee and $message->new)
                return true;

        return false;
    }

    /**
    * Vérifie si un ticket peu être modifié
    * @return bool
    **/
    public function isEditable() {
        return $this->status != self::STATUS_CLOSED;
    }

    /**
    * Retourne la liste des images associées
    * @return array
    **/
    public function getPictures() {
        
        $data = array();
        foreach(glob($this->getDirectory(true)."*.*") as $path) {
            
            $row = explode('/', $path);
            $data[] = end($row);
        }

        return $data;
    }

    /**
    * Vérifie l'existance du dossier des images
    **/
    public function checkDirectory() {
        if(!is_dir($this->getDirectory(true)))
            mkdir($this->getDirectory(true), 0777, true);
    }

    /**
    * Retourne le chemin du dossier images
    * @return string
    **/
    public function getDirectory($absolute = false) {

        $path = "/img/sav/".$this->reference."/";
        if($absolute)
            $path = _PS_ROOT_DIR_.$path;

        return $path;
    }

    /**
    * Retrouve les SAV n'ayant pas été traité depuis plusieurs jours
    * @param int $nb_days
    * @return array
    **/
    public function findLateTreatment($nb_days) {

        $date = new DateTime('today');
        $date->modify("-$nb_days days");
        $date = $date->format('d/m/Y 00:00:00');

        $data = array();
        foreach(Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE status = 1 AND date_add < '$date'") as $row)
            $data[] = new self($row[self::TABLE_PRIMARY]);

        return $data;
    }

    /**
    * Retrouve les SAV n'ayant pas été mis à jour depuis plusieurs jours
    * @param int $nb_days
    * @return array
    **/
    public function findLateUpdate($nb_days) {

        $date = new DateTime('today');
        $date->modify("-$nb_days days");
        $date = $date->format('d/m/Y 00:00:00');

        $data = array();
        foreach(Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE status = 2 AND date_upd < '$date'") as $row)
            $data[] = new self($row[self::TABLE_PRIMARY]);

        return $data;
    }

}