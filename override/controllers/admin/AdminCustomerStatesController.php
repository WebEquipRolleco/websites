<?php

class AdminCustomerStatesControllerCore extends AdminController {

    public function __construct() {

        $this->bootstrap = true;
        $this->table = 'customer_state';
        $this->className = 'CustomerState';
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os'
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        
        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_customer_state' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global')
            ),
            'risk_level' => array(
                'title' => $this->trans('Niveau de risque', array(), 'Admin.Global'),
                'align' => 'center'
            ),
            'show_customer' => array(
                'title' => $this->trans('Montrer au client', array(), 'Admin.Global'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ),
        );
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Statut client', array(), 'Admin.Shipping.Feature'),
                'icon' => 'icon-user'
            ),
            'input' => array(
 				array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Shipping.Feature'),
                    'name' => 'name',
                    'required' => true,
                    'maxlength' => 512
                ),
                array(
                    'type' => 'color',
                    'label' => $this->trans('Arrière plan', array(), 'Admin.Shipping.Feature'),
                    'name' => 'color',
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Niveau de risque', array(), 'Admin.Global'),
                    'name' => 'risk_level',
                    'options' => array(
                        'id' => 'value',
                        'name' => 'value',
                        'query' => array(
                            array('value'=>0),
                            array('value'=>1),
                            array('value'=>2),
                            array('value'=>3)
                        )
                    ),
                    'hint' => $this->trans("0 pour un risque inexistant, 3 pour un risque élevé")
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Texte clair', array(), 'Admin.Global'),
                    'name' => 'light_text',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->trans('Texte blanc pour les couleurs de fond foncées.', array(), 'Admin.Shipping.Help')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Afficher au client', array(), 'Admin.Global'),
                    'name' => 'show_customer',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->trans('Le statut sera visible par le client sur la page "mon compte".', array(), 'Admin.Shipping.Help')
                )
            )
        );

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        //$this->getFieldsValues($obj);
        return parent::renderForm();
    }

}