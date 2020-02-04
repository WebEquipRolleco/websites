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
                'align' => 'center',
                'filter_key' => 'a!reference'
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
    * OVERRIDE : Ajout référence
    **/
    public function renderView()
    {
        if (($id = Tools::getValue('id_feature'))) {
            $this->setTypeValue();
            $this->list_id = 'feature_value';
            $this->lang = true;

            // Action for list
            $this->addRowAction('edit');
            $this->addRowAction('delete');

            if (!Validate::isLoadedObject($obj = new Feature((int)$id))) {
                $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error').' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
                return;
            }

            $this->feature_name = $obj->name;
            $this->toolbar_title = $this->feature_name[$this->context->employee->id_lang];
            $this->fields_list = array(
                'id_feature_value' => array(
                    'title' => $this->trans('ID', array(), 'Admin.Global'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs'
                ),
                'reference' => array(
                    'title' => $this->trans('Référence', array(), 'Admin.Global'),
                    'align' => 'center',
                    'filter_key' => 'a!reference'
                ),
                'value' => array(
                    'title' => $this->trans('Value', array(), 'Admin.Global'),
                    'align' => 'center'
                )
            );

            $this->_where = sprintf('AND `id_feature` = %d', (int)$id);
            self::$currentIndex = self::$currentIndex.'&id_feature='.(int)$id.'&viewfeature';
            $this->processFilter();
            return AdminController::renderList();
        }
    }

    /**
    * OVERRIDE : Ajout référence + colonne d'affichage
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

    /**
    * OVERRIDE : Ajout référence
    **/
    public function initFormFeatureValue()
    {
        $this->setTypeValue();

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->trans('Feature value', array(), 'Admin.Catalog.Feature'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->trans('Feature', array(), 'Admin.Catalog.Feature'),
                    'name' => 'id_feature',
                    'options' => array(
                        'query' => Feature::getFeatures($this->context->language->id),
                        'id' => 'id_feature',
                        'name' => 'name'
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Référence', array(), 'Admin.Global'),
                    'name' => 'reference'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Value', array(), 'Admin.Global'),
                    'name' => 'value',
                    'lang' => true,
                    'size' => 33,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}',
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->trans('Save then add another value', array(), 'Admin.Catalog.Feature'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        $this->fields_value['id_feature'] = (int)Tools::getValue('id_feature');

        // Create Object FeatureValue
        $feature_value = new FeatureValue(Tools::getValue('id_feature_value'));

        $this->tpl_vars = array(
            'feature_value' => $feature_value,
        );

        $this->getlanguages();
        $helper = new HelperForm();
        $helper->show_cancel_button = true;

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex.'&token='.$this->token;
        }
        if (!Validate::isCleanHtml($back)) {
            die(Tools::displayError());
        }

        $helper->back_url = $back;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $helper->override_folder = 'feature_value/';
        $helper->id = $feature_value->id;
        $helper->toolbar_scroll = false;
        $helper->tpl_vars = $this->tpl_vars;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->fields_value = $this->getFieldsValue($feature_value);
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->title = $this->trans('Add a new feature value', array(), 'Admin.Catalog.Feature');
        $this->content .= $helper->generateForm($this->fields_form);
    }


}