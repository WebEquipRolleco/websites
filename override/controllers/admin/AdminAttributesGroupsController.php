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
    * AdminController::renderForm() override
    * @see AdminController::renderForm()
    * OVERRIDE : ajout affichage devis
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

}