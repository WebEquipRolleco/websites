<?php

class ImportFeatureGroups extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_feature';
        $data[] = "name";
        $data[] = "public_name";
        $data[] = "reference";
        $data[] = "column";
        $data[] = "delete";

        return $data;
    }

    /**
    * Import des groups de caractéristiques
    **/
    public function import() {

    	$this->openFile();  
        while($row = $this->getNextRow()) {
        	if(count($row) == count($this->getColumns())) {

        		$row = array_combine($this->getColumns(), $row);

        		$feature = new Feature($row['id_feature'], 1);
        		$update = !empty($feature->id);

        		if($feature->id and $row['delete']) {
        			$feature->delete();
        		}
        		else {

	        		$feature->id = $row['id_feature'];
	        		$feature->reference = strtoupper(substr($row['reference'], 0, 3));
	        		$feature->name = $row['name'];
	        		$feature->public_name = $row['public_name'];
	        		$feature->column = ($row['column'] ? $row['column'] : null);

	        		$feature->record($update);
	        	}

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }
}