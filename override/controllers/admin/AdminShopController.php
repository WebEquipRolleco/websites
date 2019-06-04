<?php

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;

class AdminShopController extends AdminShopControllerCore {

	/**
	* OVERRIDE : ajout de champs
	**/
	public function renderForm() {

        /** @var Shop $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Shop', array(), 'Admin.Global'),
                'icon' => 'icon-shopping-cart'
            ),
            'identifier' => 'shop_id',
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Shop name', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => array(
                        $this->trans('This field does not refer to the shop name visible in the front office.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Follow [1]this link[/1] to edit the shop name used on the front office.', array(
                            '[1]' => '<a href="'.$this->context->link->getAdminLink('AdminStores').'#store_fieldset_general">',
                            '[/1]' => '</a>'
                        ), 'Admin.Shopparameters.Help')),
                    'name' => 'name',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Préfix de la référence commande', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'reference_prefix',
                    'required' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Longueur de la référence commande', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'reference_length',
                    'required' => false,
                )
            )
        );

        $display_group_list = true;
        if ($this->display == 'edit') {
            $group = new ShopGroup($obj->id_shop_group);
            if ($group->share_customer || $group->share_order || $group->share_stock) {
                $display_group_list = false;
            }
        }

        if ($display_group_list) {
            $options = array();
            foreach (ShopGroup::getShopGroups() as $group) {
                /** @var ShopGroup $group */
                if ($this->display == 'edit' && ($group->share_customer || $group->share_order || $group->share_stock) && ShopGroup::hasDependency($group->id)) {
                    continue;
                }

                $options[] = array(
                    'id_shop_group' =>    $group->id,
                    'name' =>            $group->name,
                );
            }

            if ($this->display == 'add') {
                $group_desc = $this->trans('Warning: You won\'t be able to change the group of this shop if this shop belongs to a group with one of these options activated: Share Customers, Share Quantities or Share Orders.', array(), 'Admin.Shopparameters.Notification');
            } else {
                $group_desc = $this->trans('You can only move your shop to a shop group with all "share" options disabled -- or to a shop group with no customers/orders.', array(), 'Admin.Shopparameters.Notification');
            }

            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->trans('Shop group', array(), 'Admin.Shopparameters.Feature'),
                'desc' => $group_desc,
                'name' => 'id_shop_group',
                'options' => array(
                    'query' => $options,
                    'id' => 'id_shop_group',
                    'name' => 'name',
                ),
            );
        } else {
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_shop_group',
                'default' => $group->name
            );
            $this->fields_form['input'][] = array(
                'type' => 'textShopGroup',
                'label' => $this->trans('Shop group', array(), 'Admin.Shopparameters.Feature'),
                'desc' => $this->trans('You can\'t edit the shop group because the current shop belongs to a group with the "share" option enabled.', array(), 'Admin.Shopparameters.Help'),
                'name' => 'id_shop_group',
                'value' => $group->name
            );
        }

        $categories = Category::getRootCategories($this->context->language->id);
        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->trans('Category root', array(), 'Admin.Catalog.Feature'),
            'desc' => $this->trans('This is the root category of the store that you\'ve created. To define a new root category for your store, [1]please click here[/1].', array(
                '[1]' => '<a href="'.$this->context->link->getAdminLink('AdminCategories').'&addcategoryroot" target="_blank">',
                '[/1]' => '</a>',
            ), 'Admin.Shopparameters.Help'),
            'name' => 'id_category',
            'options' => array(
                'query' => $categories,
                'id' => 'id_category',
                'name' => 'name'
            )
        );

        if (Tools::isSubmit('id_shop')) {
            $shop = new Shop((int)Tools::getValue('id_shop'));
            $id_root = $shop->id_category;
        } else {
            $id_root = $categories[0]['id_category'];
        }


        $id_shop = (int)Tools::getValue('id_shop');
        self::$currentIndex = self::$currentIndex.'&id_shop_group='.(int)(Tools::getValue('id_shop_group') ?
            Tools::getValue('id_shop_group') : (isset($obj->id_shop_group) ? $obj->id_shop_group : Shop::getContextShopGroupID()));
        $shop = new Shop($id_shop);
        $selected_cat = Shop::getCategories($id_shop);

        if (empty($selected_cat)) {
            // get first category root and preselect all these children
            $root_categories = Category::getRootCategories();
            $root_category = new Category($root_categories[0]['id_category']);
            $children = $root_category->getAllChildren($this->context->language->id);
            $selected_cat[] = $root_categories[0]['id_category'];

            foreach ($children as $child) {
                $selected_cat[] = $child->id;
            }
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }

        $this->fields_form['input'][] = array(
            'type' => 'categories',
            'name' => 'categoryBox',
            'label' => $this->trans('Associated categories', array(), 'Admin.Catalog.Feature'),
            'tree' => array(
                'id' => 'categories-tree',
                'selected_categories' => $selected_cat,
                'root_category' => $root_category->id,
                'use_search' => true,
                'use_checkbox' => true
            ),
            'desc' => $this->trans('By selecting associated categories, you are choosing to share the categories between shops. Once associated between shops, any alteration of this category will impact every shop.', array(), 'Admin.Shopparameters.Help')
        );
        /*$this->fields_form['input'][] = array(
            'type' => 'switch',
            'label' => $this->trans('Enabled', array(), 'Admin.Global'),
            'name' => 'active',
            'required' => true,
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0
                )
            ),
            'desc' => $this->trans('Enable or disable your store?', array(), 'Admin.Shopparameters.Help')
        );*/

        $themes = (new ThemeManagerBuilder($this->context, Db::getInstance()))
                        ->buildRepository()
                        ->getList();

        $this->fields_form['input'][] = array(
            'type' => 'theme',
            'label' => $this->trans('Theme', array(), 'Admin.Design.Feature'),
            'name' => 'theme',
            'values' => $themes
        );

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        if (Shop::getTotalShops() > 1 && $obj->id) {
            $disabled = array('active' => false);
        } else {
            $disabled = false;
        }

        $import_data = array(
            'carrier' => $this->trans('Carriers', array(), 'Admin.Shipping.Feature'),
            'cms' => $this->trans('Pages', array(), 'Admin.Design.Feature'),
            'contact' => $this->trans('Contact information', array(), 'Admin.Advparameters.Feature'),
            'country' => $this->trans('Countries', array(), 'Admin.Global'),
            'currency' => $this->trans('Currencies', array(), 'Admin.Global'),
            'discount' => $this->trans('Discount prices', array(), 'Admin.Advparameters.Feature'),
            'employee' => $this->trans('Employees', array(), 'Admin.Advparameters.Feature'),
            'image' => $this->trans('Images', array(), 'Admin.Global'),
            'lang' => $this->trans('Languages', array(), 'Admin.Global'),
            'manufacturer' => $this->trans('Brands', array(), 'Admin.Global'),
            'module' => $this->trans('Modules', array(), 'Admin.Global'),
            'hook_module' => $this->trans('Module hooks', array(), 'Admin.Advparameters.Feature'),
            'meta_lang' => $this->trans('Meta information', array(), 'Admin.Advparameters.Feature'),
            'product' => $this->trans('Products', array(), 'Admin.Global'),
            'product_attribute' => $this->trans('Product combinations', array(), 'Admin.Advparameters.Feature'),
            'stock_available' => $this->trans('Available quantities for sale', array(), 'Admin.Advparameters.Feature'),
            'store' => $this->trans('Stores', array(), 'Admin.Global'),
            'warehouse' => $this->trans('Warehouses', array(), 'Admin.Advparameters.Feature'),
            'webservice_account' => $this->trans('Webservice accounts', array(), 'Admin.Advparameters.Feature'),
            'attribute_group' => $this->trans('Attribute groups', array(), 'Admin.Advparameters.Feature'),
            'feature' => $this->trans('Features', array(), 'Admin.Global'),
            'group' => $this->trans('Customer groups', array(), 'Admin.Advparameters.Feature'),
            'tax_rules_group' => $this->trans('Tax rules groups', array(), 'Admin.Advparameters.Feature'),
            'supplier' => $this->trans('Suppliers', array(), 'Admin.Global'),
            'referrer' => $this->trans('Referrers/affiliates', array(), 'Admin.Advparameters.Feature'),
            'zone' => $this->trans('Zones', array(), 'Admin.International.Feature'),
            'cart_rule' => $this->trans('Cart rules', array(), 'Admin.Advparameters.Feature'),
        );

        // Hook for duplication of shop data
        $modules_list = Hook::getHookModuleExecList('actionShopDataDuplication');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $import_data['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }

        asort($import_data);

        if (!$this->object->id) {
            $this->fields_import_form = array(
                'radio' => array(
                    'type' => 'radio',
                    'label' => $this->trans('Import data', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'useImportData',
                    'value' => 1
                ),
                'select' => array(
                    'type' => 'select',
                    'name' => 'importFromShop',
                    'label' => $this->trans('Choose the source shop', array(), 'Admin.Advparameters.Feature'),
                    'options' => array(
                        'query' => Shop::getShops(false),
                        'name' => 'name'
                    )
                ),
                'allcheckbox' => array(
                    'type' => 'checkbox',
                    'label' => $this->trans('Choose data to import', array(), 'Admin.Advparameters.Feature'),
                    'values' => $import_data
                ),
                'desc' => $this->trans('Use this option to associate data (products, modules, etc.) the same way for each selected shop.', array(), 'Admin.Advparameters.Help')
            );
        }

        if (!$obj->theme_name) {
            $themes = (new ThemeManagerBuilder($this->context, Db::getInstance()))
                            ->buildRepository()
                            ->getList();
            $theme = array_pop($themes);
            $theme_name = $theme->getName();
        } else {
            $theme_name = $obj->theme_name;
        }

        $this->fields_value = array(
            'id_shop_group' => (Tools::getValue('id_shop_group') ? Tools::getValue('id_shop_group') :
                (isset($obj->id_shop_group)) ? $obj->id_shop_group : Shop::getContextShopGroupID()),
            'id_category' => (Tools::getValue('id_category') ? Tools::getValue('id_category') :
                (isset($obj->id_category)) ? $obj->id_category : (int)Configuration::get('PS_HOME_CATEGORY')),
            'theme_name' => $theme_name,
        );

        $ids_category = array();
        $shops = Shop::getShops(false);
        foreach ($shops as $shop) {
            $ids_category[$shop['id_shop']] = $shop['id_category'];
        }

        $this->tpl_form_vars = array(
            'disabled' => $disabled,
            'checked' => (Tools::getValue('addshop') !== false) ? true : false,
            'defaultShop' => (int)Configuration::get('PS_SHOP_DEFAULT'),
            'ids_category' => $ids_category,
        );
        if (isset($this->fields_import_form)) {
            $this->tpl_form_vars = array_merge($this->tpl_form_vars, array('form_import' => $this->fields_import_form));
        }

        return AdminController::renderForm();
    }
}