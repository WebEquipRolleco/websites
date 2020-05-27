<?php

class Image extends ImageCore {

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'image',
        'primary' => 'id_image',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'cover' => array('type' => self::TYPE_BOOL, 'allow_null' => true, 'validate' => 'isBool', 'shop' => true),
            'legend' => array('type' => self::TYPE_STRING, 'lang' => true),
        ),
    );

	/**
	* Retourne le chemin du fichier 
	* @param string $type cart/home/small/medium/large
	* @return string
	**/
	public function getProductFilePath($type = null) {
		
		$path = _PS_IMG_DIR_."p/".$this->getImgPath();
		$extension = ".".$this->image_format;
		
		if($type) {
			$check_path = $path."-".$type."_default".$extension;
			if(is_file($check_path))
				return $check_path; 
		}
		
		$check_path = $path.$extension;
		if(is_file($check_path))
			return $check_path;

		return $check_path;
	}

	/**
	* Retourne l'url du fichier 
	* @param string $type cart/home/small/medium/large
	* @return string
	**/
	public function getFileUrl($type = null) {

		$path = _PS_BASE_URL_."/img/p/".$this->getImgPath();
		$extension = ".".$this->image_format;
		
		if($type) {
			$check_path = $path."-".$type."_default".$extension;
			if(is_file($check_path))
				return $check_path; 
		}

		$check_path = $path.$extension;
		if(is_file($check_path))
			return $check_path;

		return $check_path;
		
	}
}