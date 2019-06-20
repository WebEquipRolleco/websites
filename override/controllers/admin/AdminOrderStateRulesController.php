<?php

class AdminOrderStateRulesController extends AdminController {
	
	public function __construct() {

		$this->bootstrap = true;
        $this->required_database = true;
        $this->table = OrderStateRule::TABLE_NAME;
        $this->className = 'OrderStateRule';
        $this->primary = OrderStateRule::TABLE_PRIMARY;

		$this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');

        AdminController::__construct();

        $this->fields_list = array(
            OrderStateRule::TABLE_PRIMARY => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center'
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'align' => 'text-center'
            ),
            'description' => array(
                'title' => $this->trans('Description', array(), 'Admin.Global'),
                'align' => 'text-center'
            ),
            'active' => array(
                'title' => $this->trans('Actif', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false
            ),
        );
    }

    public function renderForm() {

        /** @var Customer $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $order_states = OrderState::getOrderStates(1);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Règle de redirection', array(), 'Admin.Global'),
                'icon' => 'icon-refresh'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Titre', array(), 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'col' => 4
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Description', array(), 'Admin.Global'),
                    'name' => 'description',
                    'col' => 4,
                    'rows' => 2
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Etapes', array(), 'Admin.Global'),
                    'name' => 'ids[]',
                    'col' => 4,
                    'size' => 10,
                    'options' => array(
                        'id' => 'id_order_state',
                        'name' => 'name',
                        'query' => $order_states
                    ),
                    'multiple' => true,
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Etat final', array(), 'Admin.Global'),
                    'name' => 'target_id',
                    'col' => 4,
                    'options' => array(
                        'id' => 'id_order_state',
                        'name' => 'name',
                        'query' => $order_states
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Actif', array(), 'Admin.Global'),
                    'name' => 'active',
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

        return parent::renderForm();
    }

}