<?php

class ImportCategories extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_category';
        $data[] = "name";
        $data[] = "shops";
        $data[] = "id_shop_default";
        $data[] = "id_parent";
        $data[] = "active";

        return $data;
    }

    /**
    * Import des groups d'attributs
    **/
    public function import() {

    	$this->openFile();  
        while($row = $this->getNextRow()) {
        	if(count($row) == count($this->getColumns())) {

        		$row = array_combine($this->getColumns(), $row);

        		$category = new Category($row['id_category'], 1);
        		if($category->id) {

                    $category->name = $row['name'];
                    $category->id_shop_default = (int)$row['id_shop_default'];
                    $category->id_parent = (int)$row['id_parent'];
                    $category->active = (bool)$row['active'];

	        		$category->save();

                    $category->deleteFromShop(1);
                    $category->deleteFromShop(2);
                    $category->deleteFromShop(3);

                    $ids = explode($this->delimiter, $row['shops']);
                    foreach($ids as $id)
                        Category::addToShop(array($category->id), $id);
	        	}

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }

}