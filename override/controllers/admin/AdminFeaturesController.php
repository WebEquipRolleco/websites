<?php

class AdminFeaturesController extends AdminFeaturesControllerCore {

	public function __construct() {

        $this->table = 'feature';
        $this->className = 'Feature';
        $this->list_id = 'feature';
        $this->identifier = 'id_feature';
        $this->lang = true;

        AdminController::__construct();

        $this->fields_list = array(
            'id_feature' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global'),
                'width' => 'auto',
                'align' => 'center'
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'width' => 'auto',
                'filter_key' => 'b!name',
                'align' => 'center'
            ),
            'value' => array(
                'title' => $this->trans('Values', array(), 'Admin.Global'),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'column' => array(
                'title' => $this->trans('Colonne', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'position' => array(
                'title' => $this->trans('Position', array(), 'Admin.Global'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position'
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')
            )
        );
    }

    /**
    * AdminController::renderForm() override
    * @see AdminController::renderForm()
    **/
    public function renderForm() {

        $this->toolbar_title = $this->trans('Add a new feature', array(), 'Admin.Catalog.Feature');
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Feature', array(), 'Admin.Catalog.Feature'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Référence', array(), 'Admin.Global'),
                    'name' => 'reference'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'size' => 33,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Public name', array(), 'Admin.Catalog.Feature'),
                    'name' => 'public_name',
                    'lang' => true,
                    'size' => 33,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Colonne', array(), 'Admin.Global'),
                    'name' => 'column',
                    'col' => 4,
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

        return AdminController::renderForm();
    }

}