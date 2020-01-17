<?php

class Attribute extends AttributeCore {

    /** @var string $reference Valeur d'identification des SKU **/
    public $reference;

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table'     => 'attribute',
        'primary'   => 'id_attribute',
        'multilang' => true,
        'fields'    => array(
            'reference'                 => array('type'=>self::TYPE_STRING),
            'id_attribute_group'        => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'color' => array('type'     => self::TYPE_STRING, 'validate' => 'isColor'),
            'position' => array('type'  => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
        )
    );

}