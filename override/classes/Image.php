<?php

class Image extends ImageCore {

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
}