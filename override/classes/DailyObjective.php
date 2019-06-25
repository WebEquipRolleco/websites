<?php

class DailyObjective extends ObjectModel {

	const TABLE_NAME = 'daily_objective';
	const TABLE_PRIMARY = 'id';

    /** @var int Id **/
	public $id;

    /** @var date Date **/
	public $date;

    /** @var float Value **/
	public $value;

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
            'date' 		=> array('type' => self::TYPE_DATE),
            'value'		=> array('type' => self::TYPE_FLOAT)
        )
    );

    /**
    * Retourne la liste des objectifs pour une période donnée
    * @param $date_begin mixed
    * @param $date_end mixed
    **/
    public static function findForPeriod($date_begin, $date_end) {

        $data = array();

        if(is_object($date_begin))
            $date_begin = $date_begin->format('Y-m-d');

        if(is_object($date_end))
            $date_end = $date_end->format('Y-m-d');

        $ids = Db::getInstance()->executeS("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE date >= '$date_begin' AND date <= '$date_end' ORDER BY date ASC");
        if($ids)
            foreach($ids as $row)
                $data[] = new DailyObjective($row['id']);

        return $data;
    }

    /**
    * Retourne un objectif pour une date précise
    * @param $date mixed
    **/
    public static function findOneByDate($date) {

        if(is_object($date))
            $date = $date->format('Y-m-d');

    	$id = Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE date = '$date'");
    	return new DailyObjective($id);
    }

    /**
    * Calcule le total des objectifs pour une période donnée
    **/
    public static function sumPeriod($date_begin, $date_end) {

        if(!is_string($date_begin))
            $date_begin = $date_begin->format('Y-m-d 00:00:00');

        if(!is_string($date_end))
            $date_end = $date_end->format('Y-m-d 23:59:59');

    	return (float)Db::getInstance()->getValue("SELECT SUM(value) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE date >= '$date_begin' AND date <= '$date_end'");
    }

    /**
    * Calcule le total des objectifs pour un mois précis
    **/
    public static function sumMonth($num_month, $year = null) {
    	if(!$year) $year = date('Y');
    	return Db::getInstance()->getValue("SELECT SUM(value) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE date LIKE '$year-$num_month-%'");
    }

    /**
    * Calcule le total des objectifs pour l'année en cours
    **/
    public static function countForCurrentYear() {
    	return Db::getInstance()->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE date LIKE '".date('Y')."-%'");
    }

}