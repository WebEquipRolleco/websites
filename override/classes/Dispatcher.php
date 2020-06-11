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

    /**
    * Create an url from
    * @param string $route_id Name the route
    * @param int    $id_lang
    * @param array  $params
    * @param bool   $force_routes
    * @param string $anchor Optional anchor to add at the end of this url
    * @param null   $id_shop
    * @return string
    * @throws PrestaShopException
    **/
    public function createUrl(
        $route_id,
        $id_lang = null,
        array $params = array(),
        $force_routes = false,
        $anchor = '',
        $id_shop = null
    ) {
        if ($id_lang === null) {
            $id_lang = (int)Context::getContext()->language->id;
        }
        if ($id_shop === null) {
            $id_shop = (int)Context::getContext()->shop->id;
        }

        if (!isset($this->routes[$id_shop])) {
            $this->loadRoutes($id_shop);
        }

        if (!isset($this->routes[$id_shop][$id_lang][$route_id])) {
            $query = http_build_query($params, '', '&');
            $index_link = $this->use_routes ? '' : 'index.php';
            return ($route_id == 'index') ? $index_link.(($query) ? '?'.$query : '') :
                ((trim($route_id) == '') ? '' : 'index.php?controller='.$route_id).(($query) ? '&'.$query : '').$anchor;
        }
        $route = $this->routes[$id_shop][$id_lang][$route_id];
        // Check required fields
        $query_params = isset($route['params']) ? $route['params'] : array();
        foreach ($route['keywords'] as $key => $data) {
            if (!$data['required']) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                throw new PrestaShopException('Dispatcher::createUrl() miss required parameter "'.
                    $key.'" for route "'.$route_id.'"');
            }
            if (isset($this->default_routes[$route_id])) {
                $query_params[$this->default_routes[$route_id]['keywords'][$key]['param']] = $params[$key];
            }
        }

        // Build an url which match a route
        if ($this->use_routes || $force_routes) {
            $url = $route['rule'];
            $add_param = array();

            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key])) {
                    if (!isset($this->default_routes[$route_id]['keywords'][$key])) {
                        $add_param[$key] = $value;
                    }
                } else {
                    if ($params[$key]) {
                        $replace = $route['keywords'][$key]['prepend'].$params[$key].$route['keywords'][$key]['append'];
                    } else {
                        $replace = '';
                    }
                    $url = preg_replace('#\{([^{}]*:)?'.$key.'(:[^{}]*)?\}#', $replace, $url);
                }
            }
            $url = preg_replace('#\{([^{}]*:)?[a-z0-9_]+?(:[^{}]*)?\}#', '', $url);
            if (count($add_param)) {
                $url .= '?'.http_build_query($add_param, '', '&');
            }
        } else {
            // Build a classic url index.php?controller=foo&...
            $add_params = array();
            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key]) && !isset($this->default_routes[$route_id]['keywords'][$key])) {
                    $add_params[$key] = $value;
                }
            }

            if (!empty($route['controller'])) {
                $query_params['controller'] = $route['controller'];
            }
            $query = http_build_query(array_merge($add_params, $query_params), '', '&');
            if ($this->multilang_activated) {
                $query .= (!empty($query) ? '&' : '').'id_lang='.(int)$id_lang;
            }
            $url = 'index.php?'.$query;
        }

        return $url;
    }

}