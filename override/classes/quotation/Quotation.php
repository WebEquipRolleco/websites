<?php

class Quotation extends ObjectModel {

	const TABLE_NAME = 'quotation';
	const TABLE_PRIMARY = 'id_quotation';

    const DELIMITER = ";";

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
    public $source;
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
    public $new = 1;
    public $highlight = 0;
    public $option_ids;
    public $id_shop;

    // Variables temporaires
    private $options = array();
    private $customer;
    private $employee;
    private $shop;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'reference' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'origin' => array('type' => self::TYPE_INT),
            'source' => array('type' => self::TYPE_INT),
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
            'active' => array('type' => self::TYPE_BOOL),
            'new' => array('type' => self::TYPE_BOOL),
            'highlight' => array('type' => self::TYPE_BOOL),
            'option_ids' => array('type' => self::TYPE_STRING),
            'id_shop' => array('type' => self::TYPE_INT)
        ),
    );

    /**
    * Retourne la liste des IDs options autorisés pour le devis
    * @return array
    **/
    public function getOptions() {

        if(empty($this->options) and $this->option_ids)
            $this->options = explode(self::DELIMITER, $this->option_ids);

        return $this->options;
    }

    /**
    * Retourne la boutique associée
    **/
    public function getShop() {

        if(!$this->shop and $this->id_shop)
            $this->shop = new Shop($this->id_shop);

        return $this->shop;
    }

    /**
    * Retourne l'object client
    **/
    public function getCustomer() {

        if(!$this->customer and $this->id_customer)
            $this->customer = new Customer($this->id_customer);

        return $this->customer;
    }

    /**
    * Retourne l'object employé
    **/
    public function getEmployee() {

        if(!$this->employee and $this->id_employee)
            $this->employee = new Employee($this->id_employee);

        return $this->employee;
    }

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
        $rows = Db::getInstance()->executeS("SELECT ".QuotationLine::TABLE_PRIMARY." FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." WHERE id_quotation = ".$this->id.' ORDER BY position');
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

    /**
    * Retourne la l iste des labels des sources du devis
    **/
    public static function getSources() {

        $data[1] = "Boutique";
        $data[2] = "HelloPro";
        $data[3] = "Expo Permanente";
        $data[4] = "Public Expo";

        return $data;
    }

    /**
    * Retourne le label de la source du devis
    **/
    public function getSourceLabel() {
        if(isset(self::getSources()[$this->source]))
            return self::getSources()[$this->source];
        else
            return null;
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
    * Retourne la liste des labels des origines possibles des devis
    **/
    public static function getOriginClasses() {

        $data[self::ORIGIN_MAIL] = "envelope";
        $data[self::ORIGIN_PHONE] = "phone";
        $data[self::ORIGIN_FAX] = "fax";
        $data[self::ORIGIN_OTHERS] = "question";

        return $data;
    }

    /**
    * Retourne le label de l'origine du devis
    **/
    public function getOriginLabel() {

        $origins = self::getOrigins();
        return isset($origins[$this->origin]) ? $origins[$this->origin] : null;
    }

    /**
    * Retourne la class de l'origine du devis
    **/
    public function getOriginClass() {

        $classes = self::getOriginClasses();
        return isset($classes[$this->origin]) ? $classes[$this->origin] : null;
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

    /**
    * Retourne le nombre de nouveau devis d'un client
    * @param int|null $id_customer
    * @return int
    **/
    public static function countNew($id_customer = null) {

        if(!$id_customer)
            return 0;

        return Db::getInstance()->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_customer = $id_customer AND new = 1");
    }

    /**
    * Calcul la valeur de ma marge du devis
    **/
    public function getMargin() {

        $margin = 0;
        foreach($this->getProducts() as $line)
            $margin += $line->getMargin();

        return $margin;
    }

    /**
    * Calcule le taux de marge du produit
    * @param bool $use_taxes
    * @return float
    **/
    public function getMarginRate($use_taxes = false) {
        return Tools::getMarginRate($this->getMargin(), $this->getPrice($use_taxes));
    }

    /**
    * Calcule la participation éco totale
    * @return float
    **/
    public function getEcoTax() {

        $total = 0;
        foreach($this->getProducts() as $line)
            $total += $line->eco_tax;

        return $total;
    }
    
    /**
    * Retourne les devis présents dans un panier
    * @param int $id_cart
    * @param bool $full
    * @return array
    **/
    public static function getFromCart($id_cart, $full = true) {

        $sql = "SELECT DISTINCT(q.".self::TABLE_PRIMARY.") 
            FROM "._DB_PREFIX_.self::TABLE_NAME." q, "._DB_PREFIX_.QuotationLine::TABLE_NAME." l, "._DB_PREFIX_.QuotationAssociation::TABLE_NAME." a 
            WHERE q.".self::TABLE_PRIMARY." = l.id_quotation 
            AND l.".QuotationLine::TABLE_PRIMARY." = a.id_line
            AND a.id_cart = ".$id_cart;

        $data = array();
        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = $full ? new self($row[self::TABLE_PRIMARY]) : $row[self::TABLE_PRIMARY];

        return $data;
    }

}