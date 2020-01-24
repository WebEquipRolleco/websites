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

    /**
     * Get all attributes for a given language
     *
     * @param int  $idLang  Language ID
     * @param bool $notNull Get only not null fields if true
     *
     * @return array Attributes
     */
    public static function getAttributes($idLang, $notNull = false)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
            SELECT DISTINCT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`, a.`reference` as `value_reference`
            FROM `'._DB_PREFIX_.'attribute_group` ag
            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$idLang.')
            LEFT JOIN `'._DB_PREFIX_.'attribute` a
                ON a.`id_attribute_group` = ag.`id_attribute_group`
            LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
                ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$idLang.')
            '.Shop::addSqlAssociation('attribute_group', 'ag').'
            '.Shop::addSqlAssociation('attribute', 'a').'
            '.($notNull ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL AND agl.`id_attribute_group` IS NOT NULL' : '').'
            ORDER BY agl.`name` ASC, a.`position` ASC
        ');
    }
}