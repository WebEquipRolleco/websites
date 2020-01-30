<?php

class ImportIconography extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = "id";
        $data[] = "name";
        $data[] = "title";
        $data[] = "url";
        $data[] = "height";
        $data[] = "width";
        $data[] = "product_white_list";
        $data[] = "product_black_list";
        $data[] = "category_white_list";
        $data[] = "category_black_list";
        $data[] = "supplier_white_list";
        $data[] = "supplier_black_list";
        $data[] = "position";
        $data[] = "active";
        $data[] = "id_shops";

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

        		$icon = new ProductIcon($row['id']);
                $update = !empty($icon->id);

                $icon->id = $row['id'];
                $icon->name = $row['name'];
                $icon->title = $row['title'];
                $icon->url = $row['url'];
                $icon->height = $row['height'];
                $icon->width = $row['width'];
                $icon->product_white_list = $row['product_white_list'];
                $icon->product_black_list = $row['product_black_list'];
                $icon->category_white_list = $row['category_white_list'];
                $icon->category_black_list = $row['category_black_list'];
                $icon->supplier_white_list = $row['supplier_white_list'];
                $icon->supplier_black_list = $row['supplier_black_list'];
                $icon->position = $row['position'];
                $icon->active = (bool)$row['active'];

                $icon->record($update);

                // Gestion boutiques
                if($row['id_shops'])
                    $icon->eraseShops();

                foreach(explode(',', $row['id_shops']) as $id_shop)
                    if(!$icon->hasShop($id_shop, false))
                        $icon->addShop($id_shop);

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }

}