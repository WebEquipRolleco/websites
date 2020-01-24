<?php

class FeatureValue extends FeatureValueCore {

	/** @var string $reference Valeur d'identification des SKU **/
    public $reference;

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' 	=> 'feature_value',
        'primary' 	=> 'id_feature_value',
        'multilang' => true,
        'fields' => array(
        	'reference'		=> array('type'=>self::TYPE_STRING),
            'id_feature' 	=> array('type'=>self::TYPE_INT, 'validate'=>'isUnsignedId', 'required'=>true),
            'custom' 		=> array('type'=>self::TYPE_BOOL, 'validate'=>'isBool'),

            /* Lang fields */
            'value' => array('type'=>self::TYPE_STRING, 'lang'=>true, 'validate'=>'isGenericName', 'required'=>true, 'size'=>255)
        ),
    );
    
    /**
    * Retourne toutes les valeurs
    **/
    public static function getAllValues($id_lang) {

        Return Db::getInstance()->executeS("SELECT fv.id_feature_value, fv.id_feature, fv.reference, fvl.value FROM ps_feature_value fv, ps_feature_value_lang fvl WHERE fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = $id_lang");
    }
}