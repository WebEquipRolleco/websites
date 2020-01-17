<?php

class AttributeGroup extends AttributeGroupCore {

    /** @var string $reference Valeur d'identification des SKU **/
    public $reference;

    /** @var int $column Numéro de la colonne dans laquelle sont affichés les informations **/
    public $column;

	/** @var bool $quotatation Information reprise ou nom sur les produits des devis */
    public $quotation = true;

    /**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'attribute_group',
        'primary' => 'id_attribute_group',
        'multilang' => true,
        'fields' => array(
            'reference'         => array('type'=>self::TYPE_STRING),
            'is_color_group'    => array('type'=>self::TYPE_BOOL, 'validate'=>'isBool'),
            'quotation'         => array('type'=>self::TYPE_BOOL, 'validate'=>'isBool'),
            'group_type'        => array('type'=>self::TYPE_STRING, 'required'=>true),
            'position'          => array('type'=>self::TYPE_INT, 'validate'=>'isInt'),
            'column'            => array('type'=>self::TYPE_INT, 'validate'=>'isInt'),

            /* Lang fields */
            'name'          => array('type'=>self::TYPE_STRING, 'lang'=>true, 'validate'=>'isGenericName', 'required'=>true, 'size'=>128),
            'public_name'   => array('type'=>self::TYPE_STRING, 'lang'=>true, 'validate'=>'isGenericName', 'required'=>true, 'size'=>64)
        )
    );
}