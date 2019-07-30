<?php

class AdminSuppliersController extends AdminSuppliersControllerCore {

    public function __construct() {

        $this->table = 'supplier';
        $this->className = 'Supplier';

        AdminController::__construct();

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->allow_export = true;

        $this->_defaultOrderBy = 'name';
        $this->_defaultOrderWay = 'ASC';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')
            )
        );

        $this->_select = 'COUNT(DISTINCT ps.`id_product`) AS products';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (a.`id_supplier` = ps.`id_supplier`)';
        $this->_group = 'GROUP BY a.`id_supplier`';

        $this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');

        $this->fields_list = array(
            'id_supplier' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'logo' => array('title' => $this->trans('Logo', array(), 'Admin.Global'), 'align' => 'center', 'image' => 'su', 'orderby' => false, 'search' => false),
            'reference' => array('title' => $this->trans('Référence', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global')),
            'products' => array('title' => $this->trans('Number of products', array(), 'Admin.Catalog.Feature'), 'align' => 'right', 'filter_type' => 'int', 'tmpTableFilter' => true),
            'active' => array('title' => $this->trans('Enabled', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'class' => 'fixed-width-xs')
        );
    }

	public function renderForm() {

        // loads current warehouse
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = _PS_SUPP_IMG_DIR_.$obj->id.'.jpg';
        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350,
            $this->imageType, true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $tmp_addr = new Address();
        $res = $tmp_addr->getFieldsRequiredDatabase();
        $required_fields = array();
        foreach ($res as $row) {
            $required_fields[(int)$row['id_required_field']] = $row['field_name'];
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Suppliers', array(), 'Admin.Global'),
                'icon' => 'icon-truck'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_address',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Référence', array(), 'Admin.Global'),
                    'name' => 'reference',
                    'required' => true,
                    'col' => 4
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}',
                ),
                (in_array('company', $required_fields) ?
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Company', array(), 'Admin.Global'),
                        'name' => 'company',
                        'display' => in_array('company', $required_fields),
                        'required' => in_array('company', $required_fields),
                        'maxlength' => 16,
                        'col' => 4,
                        'hint' => $this->trans('Company name for this supplier', array(), 'Admin.Catalog.Help')
                    )
                    : null
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Description', array(), 'Admin.Global'),
                    'name' => 'description',
                    'lang' => true,
                    'hint' => array(
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}',
                        $this->trans('Will appear in the list of suppliers.', array(), 'Admin.Catalog.Help')
                    ),
                    'autoload_rte' => 'rte' //Enable TinyMCE editor for short description
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('E-mails', array(), 'Admin.Global'),
                    'name' => 'emails',
                    'col' => 4,
                    'hint' => $this->trans('Email(s) séparé(s) par une virgule, sans espaces', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('E-mail spécifique SAV', array(), 'Admin.Global'),
                    'name' => 'email_sav',
                    'col' => 4,
                    'hint' => $this->trans('Email de contact par défaut lors des traitements SAV', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Phone', array(), 'Admin.Global'),
                    'name' => 'phone',
                    'required' => in_array('phone', $required_fields),
                    'maxlength' => 16,
                    'col' => 4,
                    'hint' => $this->trans('Phone number for this supplier', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Mobile phone', array(), 'Admin.Global'),
                    'name' => 'phone_mobile',
                    'required' => in_array('phone_mobile', $required_fields),
                    'maxlength' => 16,
                    'col' => 4,
                    'hint' => $this->trans('Mobile phone number for this supplier.', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address', array(), 'Admin.Global'),
                    'name' => 'address',
                    'maxlength' => 128,
                    'col' => 6,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address (2)', array(), 'Admin.Global'),
                    'name' => 'address2',
                    'required' => in_array('address2', $required_fields),
                    'col' => 6,
                    'maxlength' => 128,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                    'name' => 'postcode',
                    'required' => in_array('postcode', $required_fields),
                    'maxlength' => 12,
                    'col' => 2,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('City', array(), 'Admin.Global'),
                    'name' => 'city',
                    'maxlength' => 32,
                    'col' => 4,
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Country', array(), 'Admin.Global'),
                    'name' => 'id_country',
                    'required' => true,
                    'col' => 4,
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id, false),
                        'id' => 'id_country',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('State', array(), 'Admin.Global'),
                    'name' => 'id_state',
                    'col' => 4,
                    'options' => array(
                        'id' => 'id_state',
                        'query' => array(),
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Logo', array(), 'Admin.Global'),
                    'name' => 'logo',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'hint' => $this->trans('Upload a supplier logo from your computer.', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta title', array(), 'Admin.Global'),
                    'name' => 'meta_title',
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta description', array(), 'Admin.Global'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'col' => 6,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->trans('Meta keywords', array(), 'Admin.Global'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'col' => 6,
                    'hint' => array(
                        $this->trans('To add "tags" click in the field, write something and then press "Enter".', array(), 'Admin.Catalog.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Envoi BC', array(), 'Admin.Actions'),
                    'name' => 'BC',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'BC_on',
                            'value' => 1,
                            'label' => $this->trans('Oui', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'BC_off',
                            'value' => 0,
                            'label' => $this->trans('Non', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Envoi BL', array(), 'Admin.Actions'),
                    'name' => 'BL',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'BC_on',
                            'value' => 1,
                            'label' => $this->trans('Oui', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'BC_off',
                            'value' => 0,
                            'label' => $this->trans('Non', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Enable', array(), 'Admin.Actions'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        // loads current address for this supplier - if possible
        $address = null;
        if (isset($obj->id)) {
            $id_address = Address::getAddressIdBySupplierId($obj->id);

            if ($id_address > 0) {
                $address = new Address((int)$id_address);
            }
        }

        // force specific fields values (address)
        if ($address != null) {
            $this->fields_value = array(
                'id_address' => $address->id,
                'phone' => $address->phone,
                'phone_mobile' => $address->phone_mobile,
                'address' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'id_country' => $address->id_country,
                'id_state' => $address->id_state,
            );
        } else {
            $this->fields_value = array(
                'id_address' => 0,
                'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
            );
        }


        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        return AdminController::renderForm();
    }
}