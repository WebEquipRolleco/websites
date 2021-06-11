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
    public $source = 1;
    public $email;
    public $phone;
    public $fax;
    public $date_begin;
    public $date_add;
    public $date_end;
    public $date_recall;
    public $comment;
    public $details;
    public $id_employee;
    public $active = 1;
    public $new = 1;
    public $highlight = 0;
    public $option_ids;
    public $document_ids;
    public $id_shop;
    public $secure_key;
    public $mail_sent = false;

    // Variables temporaires
    private $options = array();
    private $documents = array();
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
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'fax' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_begin' => array('type' => self::TYPE_DATE),
            'date_end' => array('type' => self::TYPE_DATE),
            'date_recall' => array('type' => self::TYPE_DATE),
            'comment' => array('type' => self::TYPE_STRING),
            'details' => array('type' => self::TYPE_STRING),
            'id_employee' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL),
            'new' => array('type' => self::TYPE_BOOL),
            'highlight' => array('type' => self::TYPE_BOOL),
            'option_ids' => array('type' => self::TYPE_STRING),
            'document_ids' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'id_shop' => array('type' => self::TYPE_INT),
            'secure_key' => array('type' => self::TYPE_STRING),
            'mail_sent' => array('type' => self::TYPE_BOOL)
        ),
    );

    /**
    * Initialisation de la clé de sécurité
    **/
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null) {
        
        parent::__construct($id, $id_lang, $id_shop, $translator);
        if(!$this->secure_key) $this->secure_key = uniqid();
    }

    /**
    * Efface le contenu de la table
    **/
    public static function erazeContent() {
        Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME);
    }

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
    * Retourne la liste des noms de documents pouvant être téléchargés dans le devis
    * @return array
    **/
    public function getDocuments() {

        if($this->document_ids and empty($this->documents))
            $this->documents = explode(self::DELIMITER, $this->document_ids);

        return $this->documents;
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

        if($this->id)
            foreach(Db::getInstance()->executeS("SELECT ".QuotationLine::TABLE_PRIMARY." FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." WHERE id_quotation = ".$this->id.' ORDER BY position') as $row)
                $data[] = new QuotationLine($row[QuotationLine::TABLE_PRIMARY]);

        return $data;
    }

    /**
    * Retourne la liste des labels des états devis
    * @return array
    **/
    public static function getStates() {

        $data[self::STATUS_WAITING] = "En attente";
        $data[self::STATUS_VALIDATED] = "Accepté";
        $data[self::STATUS_REFUSED] = "Refusé";
        $data[self::STATUS_OVER] = "Terminé";

        return $data;
    }

    /**
    * Retourne la l iste des labels des sources du devis
    * @return array
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
    * @return string
    **/
    public function getSourceLabel() {
        if(isset(self::getSources()[$this->source]))
            return self::getSources()[$this->source];
        else
            return null;
    }

    /**
    * Retourne le prix total du devis
    * @param bool $use_tax
    * @param bool $eco_tax
    * @return float
    **/
    public function getPrice($use_tax = false, $fees = false, $eco_tax = false) {

        $price = 0;
        foreach($this->getProducts() as $line)
            $price += $line->getPrice($use_tax, $fees, $eco_tax);

        return $price;
    }

    public function getSum($use_tax = true, $fees = true, $eco_tax = true) {
        $sql = "SELECT SUM() FROM ps_order_detail WHERE id_order IN (".implode(',', $ids).")";
    }

    /**
    * Retourne le montant total des frais du devis
    * @return float
    **/
    public function getBuyingFees($use_taxes = false) {

        $price = 0;
        foreach($this->getProducts() as $line)
            $price += $line->buying_fees;

        if($use_taxes)
            $price *= 1.2;

        return $price;
    }

    /**
    * Retourne le montant de la TVA
    * @return float
    **/
    public function getTVA() {

        $taxes = $this->getPrice(true) - $this->getPrice();
        $taxes -= $this->getEcoTax();

        return $taxes;
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
    * @return array
    **/
    public function getEmails() {

        $emails = array();

        if($this->email)
            $emails = explode(',', $this->email);

        $customer = $this->getCustomer();
        if($customer) $emails[] = $customer->email;

        return $emails;
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

        if(isset($options['highlight']))
            $sql .= " AND highlight = ".(int)$options['highlight'];

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
            $data[] = new Quotation($row[self::TABLE_PRIMARY]);

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

        $date = date('Y-m-d H:i:s');
        return Db::getInstance()->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_customer = $id_customer AND (new = 1 OR highlight = 1) AND (date_begin > '$date' OR date_begin IS NULL) AND (date_end < '$date' OR date_end IS NULL)");
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
            $total += $line->getEcoTax();

        return $total;
    }

    /**
    * Génère une référence en fonction de la boutique
    * @param bool $update_shop
    **/
    public function generateReference($update_shop = false) {

        $prefix = Configuration::get('QUOTATION_PREFIX');
        $index = (int)Configuration::get('QUOTATION_INDEX');

        $this->reference = $prefix.$index;
        
        if($update_shop) {
            
            $index++;
            Configuration::updateValue('QUOTATION_INDEX', $index);
        }
    }

    /**
    * Retourne le lien d'ajout panier
    * @return string
    **/
    public function getLink() {

        $link = new Link();
        return $link->getPageLink('QuotationRegistration&accept='.$this->reference.'&key='.$this->secure_key, null, 1, null, false, $this->id_shop)."&utm_source=devis&utm_medium=devis";
    }

    /**
    * Retourne la liste des devis à relancer
    **/
    public static function needToRecall() {

        $options['date_recall'] = date('Y-m-d');
        $options['states'] = array(self::STATUS_WAITING);

        return self::find($options);
    }
    
    /**
    * Retourne la commande associée au devis (si existe)
    * @return Order
    **/
    public function getOrder() {

        if($this->id) {
            $id = Db::getInstance()->getValue("SELECT o.id_order FROM "._DB_PREFIX_."orders o, "._DB_PREFIX_.QuotationAssociation::TABLE_NAME." qa WHERE o.id_cart = qa.id_cart AND qa.id_quotation = ".$this->id);
            if($id)
                return new Order($id);
        }
        
        return false;
    }

    /**
    * Retourne le dossier des images du devis
    * @param bool $absolute Chemin relatif ou absolu 
    * @return string
    **/
    public function getDirectory($absolute = false) {

        $path = '/img/quotations/'.$this->id."/";

        if(!is_dir(_PS_ROOT_DIR_.$path))
            mkdir(_PS_ROOT_DIR_.$path, 0777, true);

        if($absolute)
            $path = _PS_ROOT_DIR_.$path;

        return $path;
    }
}