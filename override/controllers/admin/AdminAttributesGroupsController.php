<?php

class AdminAttributesGroupsController extends AdminAttributesGroupsControllerCore {

	/**
	* OVERRIDE : ajout affichage devis
	**/
	public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'attribute_group';
        $this->list_id = 'attribute_group';
        $this->identifier = 'id_attribute_group';
        $this->className = 'AttributeGroup';
        $this->lang = true;
        $this->_defaultOrderBy = 'position';

        AdminController::__construct();

        $this->fields_list = array(
            'id_attribute_group' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global'),
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'filter_key' => 'b!name',
            ),
            'count_values' => array(
                'title' => $this->trans('Values', array(), 'Admin.Catalog.Feature'),
                'align' => 'center',
                'orderby' => false,
                'search' => false
            ),
            'quotation' => array(
            	'title' => $this->trans('Devis', array(), 'Admin.Global'), 
            	'align' => 'center', 
            	'active' => 'status',
                'type' => 'bool', 
            ),
            'column' => array(
                'title' => $this->trans('Colonne', array(), 'Admin.Global'),
                'filter_key' => 'a!column',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'position' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info')
            )
        );
        $this->fieldImageSettings = array('name' => 'texture', 'dir' => 'co');
    }

    /**
    * @OVERRIDE : Ajout référence
    **/
    public function renderView() {

        if(($id = Tools::getValue('id_attribute_group'))) {
            $this->table      = 'attribute';
            $this->className  = 'Attribute';
            $this->identifier = 'id_attribute';
            $this->position_identifier = 'id_attribute';
            $this->position_group_identifier = 'id_attribute_group';
            $this->list_id    = 'attribute_values';
            $this->lang       = true;

            $this->context->smarty->assign(array(
                'current' => self::$currentIndex.'&id_attribute_group='.(int)$id.'&viewattribute_group'
            ));

            if (!Validate::isLoadedObject($obj = new AttributeGroup((int)$id))) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Catalog.Notification').
                    ' <b>'.$this->table.'</b> '.
                    $this->trans('(cannot load object)', array(), 'Admin.Catalog.Notification');
                return;
            }

            $this->attribute_name = $obj->name;
            $this->fields_list = array(
                'id_attribute' => array(
                    'title' => $this->trans('ID', array(), 'Admin.Global'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs'
                ),
                'reference' => array(
                    'title' => $this->trans('Référence', array(), 'Admin.Catalog.Feature'),
                    'width' => 'auto',
                    'align' => 'center',
                    'filter_key' => 'b!reference'
                ),
                'name' => array(
                    'title' => $this->trans('Value', array(), 'Admin.Catalog.Feature'),
                    'width' => 'auto',
                    'align' => 'center',
                    'filter_key' => 'b!name'
                )
            );

            if ($obj->group_type == 'color') {
                $this->fields_list['color'] = array(
                    'title' => $this->trans('Color', array(), 'Admin.Catalog.Feature'),
                    'filter_key' => 'a!color',
                );
            }

            $this->fields_list['position'] = array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'class' => 'fixed-width-md'
            );

            $this->addRowAction('edit');
            $this->addRowAction('delete');

            $this->_where = 'AND a.`id_attribute_group` = '.(int)$id;
            $this->_orderBy = 'position';

            self::$currentIndex = self::$currentIndex.'&id_attribute_group='.(int)$id.'&viewattribute_group';
            $this->processFilter();
            return AdminController::renderList();
        }
    }

	/**
    * OVERRIDE : Ajout référence + affichage devis
    **/
    public function renderForm() {

        $this->table = 'attribute_group';
        $this->identifier = 'id_attribute_group';

        $group_type = array(
            array(
                'id' => 'select',
                'name' => $this->trans('Drop-down list', array(), 'Admin.Global')
            ),
            array(
                'id' => 'radio',
                'name' => $this->trans('Radio buttons', array(), 'Admin.Global')
            ),
            array(
                'id' => 'color',
                'name' => $this->trans('Color or texture', array(), 'Admin.Catalog.Feature')
            ),
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Attributes', array(), 'Admin.Catalog.Feature'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('Your internal name for this attribute.', array(), 'Admin.Catalog.Help').'&nbsp;'.$this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Référence', array(), 'Admin.Global'),
                    'name' => 'reference',
                    'col' => '4'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Public name', array(), 'Admin.Catalog.Feature'),
                    'name' => 'public_name',
                    'lang' => true,
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('The public name for this attribute, displayed to the customers.', array(), 'Admin.Catalog.Help').'&nbsp;'.$this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Attribute type', array(), 'Admin.Catalog.Feature'),
                    'name' => 'group_type',
                    'required' => true,
                    'options' => array(
                        'query' => $group_type,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'col' => '2',
                    'hint' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', array(), 'Admin.Catalog.Help')
                ),
                array(
	                'type' => 'switch',
	                'label' => $this->trans('Devis', array(), 'Admin.Global'),
	                'name' => 'quotation',
	                'required' => false,
	                'is_bool' => true,
	                'values' => array(
	                    array(
	                        'id' => 'quotation_on',
	                        'value' => 1,
	                        'label' => $this->trans('Yes', array(), 'Admin.Global')
	                    ),
	                    array(
	                        'id' => 'quotation_off',
	                        'value' => 0,
	                        'label' => $this->trans('No', array(), 'Admin.Global')
	                    )
	                ),
	                'hint' => $this->trans('Afficher dans les produits des devis', array(), 'Admin.Advparameters.Help')
	            ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Colonne', array(), 'Admin.Global'),
                    'name' => 'column',
                    'col' => '4',
                    'hint' => $this->trans('Colonne 1 : Dimensions | Colonne 2 : Délai', array(), 'Admin.Catalog.Help')
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        return AdminController::renderForm();
    }

    /**
    * Override : Ajout référence
    **/
    public function renderFormAttributes()
    {
        $attributes_groups = AttributeGroup::getAttributesGroups($this->context->language->id);

        $this->table = 'attribute';
        $this->identifier = 'id_attribute';

        $this->show_form_cancel_button = true;
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Values', array(), 'Admin.Global'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->trans('Attribute group', array(), 'Admin.Catalog.Feature'),
                    'name' => 'id_attribute_group',
                    'required' => true,
                    'options' => array(
                        'query' => $attributes_groups,
                        'id' => 'id_attribute_group',
                        'name' => 'name'
                    ),
                    'hint' => $this->trans('Choose the attribute group for this value.', array(), 'Admin.Catalog.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Référence', array(), 'Admin.Global'),
                    'name' => 'reference'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Value', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                )
            )
        );

        if (Shop::isFeatureActive()) {
            // We get all associated shops for all attribute groups, because we will disable group shops
            // for attributes that the selected attribute group don't support
            $sql = 'SELECT id_attribute_group, id_shop FROM '._DB_PREFIX_.'attribute_group_shop';
            $associations = array();
            foreach (Db::getInstance()->executeS($sql) as $row) {
                $associations[$row['id_attribute_group']][] = $row['id_shop'];
            }

            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
                'values' => Shop::getTree()
            );
        } else {
            $associations = array();
        }

        $this->fields_form['shop_associations'] = json_encode($associations);

        $this->fields_form['input'][] = array(
            'type' => 'color',
            'label' => $this->trans('Color', array(), 'Admin.Catalog.Feature'),
            'name' => 'color',
            'hint' => $this->trans('Choose a color with the color picker, or enter an HTML color (e.g. "lightblue", "#CC6600").', array(), 'Admin.Catalog.Help')
        );

        $this->fields_form['input'][] = array(
            'type' => 'file',
            'label' => $this->trans('Texture', array(), 'Admin.Catalog.Feature'),
            'name' => 'texture',
            'hint' => array(
                $this->trans('Upload an image file containing the color texture from your computer.', array(), 'Admin.Catalog.Help'),
                $this->trans('This will override the HTML color!', array(), 'Admin.Catalog.Help')
            )
        );

        $this->fields_form['input'][] = array(
            'type' => 'current_texture',
            'label' => $this->trans('Current texture', array(), 'Admin.Catalog.Feature'),
            'name' => 'current_texture'
        );

        $this->fields_form['input'][] = array(
            'type' => 'closediv',
            'name' => ''
        );

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        $this->fields_form['buttons'] = array(
            'save-and-stay' => array(
                'title' => $this->trans('Save then add another value', array(), 'Admin.Catalog.Feature'),
                'name' => 'submitAdd'.$this->table.'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save'
            )
        );

        $this->fields_value['id_attribute_group'] = (int)Tools::getValue('id_attribute_group');

        // Override var of Controller
        $this->table = 'attribute';
        $this->className = 'Attribute';
        $this->identifier = 'id_attribute';
        $this->lang = true;
        $this->tpl_folder = 'attributes/';

        // Create object Attribute
        if (!$obj = new Attribute((int)Tools::getValue($this->identifier))) {
            return;
        }

        $str_attributes_groups = '';
        foreach ($attributes_groups as $attribute_group) {
            $str_attributes_groups .= '"'.$attribute_group['id_attribute_group'].'" : '.($attribute_group['group_type'] == 'color' ? '1' : '0').', ';
        }

        $image = '../img/'.$this->fieldImageSettings['dir'].'/'.(int)$obj->id.'.jpg';

        $this->tpl_form_vars = array(
            'strAttributesGroups' => $str_attributes_groups,
            'colorAttributeProperties' => Validate::isLoadedObject($obj) && $obj->isColorAttribute(),
            'imageTextureExists' => file_exists(_PS_IMG_DIR_.$this->fieldImageSettings['dir'].'/'.(int)$obj->id.'.jpg'),
            'imageTexture' => $image,
            'imageTextureUrl' => Tools::safeOutput($_SERVER['REQUEST_URI']).'&deleteImage=1'
        );

        return AdminController::renderForm();
    }

}