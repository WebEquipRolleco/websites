<?php

class Feature extends FeatureCore {

	/** @var int $column Numéro de la colonne dans laquelle sont affichés les informations **/
    public $column;

    /** @var string $public_name Valeur affiché dans le tableau de la page produit **/
    public $public_name;
    
    /**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'feature',
        'primary' => 'id_feature',
        'multilang' => true,
        'fields' => array(
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'column' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'public_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );
}