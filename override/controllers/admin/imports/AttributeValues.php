<?php

class ImportAttributeValues extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_attribute';
        $data[] = "id_attribute_group";
        $data[] = "reference";
        $data[] = "value";
        $data[] = "_nb_use";
        $data[] = "delete";

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

        		$attribute = new Attribute($row['id_attribute'], 1);
        		$update = !empty($attribute->id);

        		if($attribute->id and $row['delete']) {
        			$attribute->delete();
        		}
        		else {

                    $attribute->id = $row['id_attribute'];
	        		$attribute->id_attribute_group = $row['id_attribute_group'];
	        		$attribute->reference = strtoupper(substr($row['reference'], 0, 3));
	        		$attribute->value = $row['value'];

	        		$attribute->record($update);
	        	}

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }

}