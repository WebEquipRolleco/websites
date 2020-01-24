<?php

class ImportAttributeGroups extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_attribute_group';
        $data[] = "name";
        $data[] = "public_name";
        $data[] = "reference";
        $data[] = "quotation";
        $data[] = "column";
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

        		$group = new AttributeGroup($row['id_attribute_group'], 1);
        		$update = !empty($group->id);

        		if($group->id and $row['delete']) {
        			$group->delete();
        		}
        		else {

	        		$group->id = $row['id_attribute_group'];
	        		$group->reference = strtoupper(substr($row['reference'], 0, 3));
	        		$group->name = $row['name'];
	        		$group->public_name = $row['public_name'];
	        		$group->quotation = (bool)$row['quotation'];
	        		$group->column = ($row['column'] ? $row['column'] : null);
	        		$group->group_type = "select";

	        		$group->record($update);
	        	}

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }

}