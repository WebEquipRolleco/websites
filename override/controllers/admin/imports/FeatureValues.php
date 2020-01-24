<?php

class ImportFeatureValues extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_feature_value';
        $data[] = "id_feature";
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

        		$value = new FeatureValue($row['id_feature_value'], 1);
        		$update = !empty($value->id);

        		if($value->id and $row['delete']) {
        			$value->delete();
        		}
        		else {

                    $value->id = $row['id_feature_value'];
	        		$value->id_feature = $row['id_feature'];
	        		$value->reference = strtoupper(substr($row['reference'], 0, 3));
	        		$value->value = $row['value'];

	        		$value->record($update);
	        	}

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }

}