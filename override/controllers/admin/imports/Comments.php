<?php

class ImportComments extends Import {
	
	/**
    * Retourne l'alias des colonnes
    * @return array
    **/
    private function getColumns() {

        $data[] = 'id_product';
        $data[] = "id_combination";
        $data[] = '_absolute_reference';
        $data[] = "id_shop";
        $data[] = "comment_1";
        $data[] = "comment_2";

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

        		if($row['id_product']) {
                    if(!empty($row['id_combination']))
                        Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET comment_1 = '".pSql(utf8_encode($row['comment_1']))."', comment_2 = '".pSql(utf8_encode($row['comment_2']))."' WHERE id_product = ".$row['id_product']." AND id_product_attribute = ".$row['id_combination']." AND id_shop = ".$row['id_shop']);
                    else
                        Db::getInstance()->execute("UPDATE ps_product_shop SET comment_1 = '".pSql(utf8_encode($row['comment_1']))."', comment_2 = '".pSql(utf8_encode($row['comment_2']))."' WHERE id_product = '".$row['id_product']."' AND id_shop = ".$row['id_shop']);
                }

	        	$this->nb_lines++;
        	}
        	else
        		$this->has_errors = true;
        }

        return true;
    }

}