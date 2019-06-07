<?php 

class AdminCustomerTypesController extends AdminController {

	public function __construct() {

        $this->bootstrap = true;
        $this->table = 'account_type';
        $this->className = 'AccountType';
        $this->lang = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os'
        );

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_account_type' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global')
            ),
            'extra_information' => array(
                'title' => $this->trans('Informations supplémentaires', array(), 'Admin.Global'),
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
                    'type' => 'switch',
                    'label' => $this->trans('Informations supplémentaires', array(), 'Admin.Global'),
                    'name' => 'extra_information',
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
                    )
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