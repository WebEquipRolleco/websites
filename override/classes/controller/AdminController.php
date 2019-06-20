<?php

class AdminController extends AdminControllerCore {

	/**
    * Return field value if possible (both classical and multilingual fields)
    *
    * Case 1 : Return value if present in $_POST / $_GET
    * Case 2 : Return object value
    *
    * @param ObjectModel $obj Object
    * @param string $key Field name
    * @param int|null $id_lang Language id (optional)
    * @return string
    **/
    public function getFieldValue($obj, $key, $id_lang = null) {

        if ($id_lang) {
            $default_value = (isset($obj->id) && $obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : false;
        } else {
            $default_value = isset($obj->{$key}) ? $obj->{$key} : false;
            if(!$default_value) {
                $key = str_replace('[]', '', $key);
                $default_value = isset($obj->{$key}) ? $obj->{$key} : false;
            }
        }

        return Tools::getValue($key.($id_lang ? '_'.$id_lang : ''), $default_value);
    }

}