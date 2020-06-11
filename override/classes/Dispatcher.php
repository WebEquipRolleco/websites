<?php

class Dispatcher extends DispatcherCore {

	/**
     * @var array List of default routes
     */
    public $default_routes = array(
        'category_rule' => array(
            'controller' =>    'category',
            'rule' =>        '{id}-{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_category'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'supplier_rule' => array(
            'controller' =>    'supplier',
            'rule' =>        '{id}__{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_supplier'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'manufacturer_rule' => array(
            'controller' =>    'manufacturer',
            'rule' =>        '{id}_{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_manufacturer'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'cms_rule' => array(
            'controller' =>    'cms',
            'rule' =>        'content/{id}-{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_cms'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'cms_category_rule' => array(
            'controller' =>    'cms',
            'rule' =>        'content/category/{id}-{rewrite}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_cms_category'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'module' => array(
            'controller' =>    null,
            'rule' =>        'module/{module}{/:controller}',
            'keywords' => array(
                'module' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'),
                'controller' =>        array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
            ),
            'params' => array(
                'fc' => 'module',
            ),
        ),
        'product_rule' => array(
            'controller' =>    'product',
            'rule' =>        '{id}-{rewrite}.html',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_product'),
                'id_product_attribute' => array('regexp' => '[0-9]+'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'rewrite'),
                'ean13' =>        array('regexp' => '[0-9\pL]*'),
                'category' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'categories' =>        array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                'reference' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'manufacturer' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'supplier' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'price' =>            array('regexp' => '[0-9\.,]*'),
                'tags' =>            array('regexp' => '[a-zA-Z0-9-\pL]*'),
            ),
        ),
        /* Must be after the product and category rules in order to avoid conflict */
        'layered_rule' => array(
            'controller' =>    'category',
            'rule' =>        '{id}-{rewrite}{/:selected_filters}',
            'keywords' => array(
                'id' =>            array('regexp' => '[0-9]+', 'param' => 'id_category'),
                /* Selected filters is used by the module blocklayered */
                'selected_filters' =>    array('regexp' => '.*', 'param' => 'selected_filters'),
                'rewrite' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
    );

}