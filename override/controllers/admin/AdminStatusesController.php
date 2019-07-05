<?php 

class AdminStatusesController extends AdminStatusesControllerCore {

	/**
     * init all variables to render the order status list
     */
    protected function initOrderStatutsList()
    {
        $this->fields_list = array(
            'id_order_state' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'width' => 'auto',
                'color' => 'color'
            ),
            'logo' => array(
                'title' => $this->trans('Icon', array(), 'Admin.Shopparameters.Feature'),
                'align' => 'text-center',
                'image' => 'os',
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
            'send_email' => array(
                'title' => $this->trans('Send email to customer', array(), 'Admin.Shopparameters.Feature'),
                'align' => 'text-center',
                'active' => 'sendEmail',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'delivery' => array(
                'title' => $this->trans('Delivery', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'delivery',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            )
            ,
            'invoice' => array(
                'title' => $this->trans('Invoice', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'invoice',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'term_of_use' => array(
                'title' => $this->trans('Conditions de ventes', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'term_of_use',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'proforma' => array(
                'title' => $this->trans('Proforma', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'proforma',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'rollcash' => array(
                'title' => $this->trans('Rollcash', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'rollcash',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'template' => array(
                'title' => $this->trans('Email template', array(), 'Admin.Shopparameters.Feature')
            )
        );
    }

	public function renderForm()
    {
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->trans('Order status', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-time'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Status name', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->trans('Order status (e.g. \'Pending\').', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Invalid characters: numbers and', array(), 'Admin.Shopparameters.Help').' !<>,;?=+()@#"{}_$%:'
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Icon', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'icon',
                    'hint' => $this->trans('Upload an icon from your computer (File type: .gif, suggested size: 16x16).', array(), 'Admin.Shopparameters.Help')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->trans('Color', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'color',
                    'hint' => $this->trans('Status will be highlighted in this color. HTML colors only.', array(), 'Admin.Shopparameters.Help').' "lightblue", "#CC6600")'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'logable',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Consider the associated order as validated.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'proforma',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Considérer la commande associée comme Proforma.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'invoice',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Allow a customer to download and view PDF versions of his/her invoices.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'hidden',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Hide this status in all customer orders.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'send_email',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Send an email to the customer when his/her order status has changed.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'pdf_invoice',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->trans('Attach invoice PDF to email.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'term_of_use',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->trans("Joindre le PDF de conditions de ventes à l'e-mail.", array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'shipped',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->trans('Set the order as shipped.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'paid',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Set the order as paid.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'rollcash',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Transférer dans la cagnote Rollcash.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select_template',
                    'label' => $this->trans('Template', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'template',
                    'lang' => true,
                    'options' => array(
                        'query' => $this->getTemplates(),
                        'id' => 'id',
                        'name' => 'name',
                        'folder' => 'folder'
                    ),
                    'hint' => array(
                        $this->trans('Only letters, numbers and underscores ("_") are allowed.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Email template for both .html and .txt.', array(), 'Admin.Shopparameters.Help')
                    )
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        if (Tools::isSubmit('updateorder_state') || Tools::isSubmit('addorder_state')) {
            return $this->renderOrderStatusForm();
        } elseif (Tools::isSubmit('updateorder_return_state') || Tools::isSubmit('addorder_return_state')) {
            return $this->renderOrderReturnsForm();
        } else {
            return AdminController::renderForm();
        }
    }

    protected function renderOrderStatusForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'logable_on' => $this->getFieldValue($obj, 'logable'),
            'proforma_on' => $this->getFieldValue($obj, 'proforma'),
            'invoice_on' => $this->getFieldValue($obj, 'invoice'),
            'hidden_on' => $this->getFieldValue($obj, 'hidden'),
            'send_email_on' => $this->getFieldValue($obj, 'send_email'),
            'shipped_on' => $this->getFieldValue($obj, 'shipped'),
            'paid_on' => $this->getFieldValue($obj, 'paid'),
            'delivery_on' => $this->getFieldValue($obj, 'delivery'),
            'pdf_delivery_on' => $this->getFieldValue($obj, 'pdf_delivery'),
            'pdf_invoice_on' => $this->getFieldValue($obj, 'pdf_invoice'),
            'term_of_use_on' => $this->getFieldValue($obj, 'term_of_use'),
            'rollcash_on' => $this->getFieldValue($obj, 'rollcash')
        );

        if ($this->getFieldValue($obj, 'color') !== false) {
            $this->fields_value['color'] = $this->getFieldValue($obj, 'color');
        } else {
            $this->fields_value['color'] = "#ffffff";
        }

        return AdminController::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAddorder_return_state')) {
            $id_order_return_state = Tools::getValue('id_order_return_state');

            // Create Object OrderReturnState
            $order_return_state = new OrderReturnState((int)$id_order_return_state);

            $order_return_state->color = Tools::getValue('color');
            $order_return_state->name = array();
            foreach (Language::getIDs(false) as $id_lang) {
                $order_return_state->name[$id_lang] = Tools::getValue('name_'.$id_lang);
            }

            // Update object
            if (!$order_return_state->save()) {
                $this->errors[] = $this->trans('An error has occurred: Can\'t save the current order\'s return status.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submitBulkdeleteorder_return_state')) {
            $this->className = 'OrderReturnState';
            $this->table = 'order_return_state';
            $this->boxes = Tools::getValue('order_return_stateBox');
            AdminController::processBulkDelete();
        }

        if (Tools::isSubmit('deleteorder_return_state')) {
            $id_order_return_state = Tools::getValue('id_order_return_state');

            // Create Object OrderReturnState
            $order_return_state = new OrderReturnState((int)$id_order_return_state);

            if (!$order_return_state->delete()) {
                $this->errors[] = $this->trans('An error has occurred: Can\'t delete the current order\'s return status.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $this->deleted = false; // Disabling saving historisation
            $_POST['invoice'] = (int)Tools::getValue('invoice_on');
            $_POST['logable'] = (int)Tools::getValue('logable_on');
            $_POST['proforma'] = (int)Tools::getValue('proforma_on');
            $_POST['send_email'] = (int)Tools::getValue('send_email_on');
            $_POST['hidden'] = (int)Tools::getValue('hidden_on');
            $_POST['shipped'] = (int)Tools::getValue('shipped_on');
            $_POST['paid'] = (int)Tools::getValue('paid_on');
            $_POST['delivery'] = (int)Tools::getValue('delivery_on');
            $_POST['pdf_delivery'] = (int)Tools::getValue('pdf_delivery_on');
            $_POST['pdf_invoice'] = (int)Tools::getValue('pdf_invoice_on');
            $_POST['term_of_use'] = (int)Tools::getValue('term_of_use_on');
            $_POST['rollcash'] = (int)Tools::getValue('rollcash_on');
            if (!$_POST['send_email']) {
                foreach (Language::getIDs(false) as $id_lang) {
                    $_POST['template_'.$id_lang] = '';
                }
            }

            return AdminController::postProcess();
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            $order_state = new OrderState(Tools::getValue('id_order_state'), $this->context->language->id);
            if (!$order_state->isRemovable()) {
                $this->errors[] = $this->trans('For security reasons, you cannot delete default order statuses.', array(), 'Admin.Shopparameters.Notification');
            } else {
                return AdminController::postProcess();
            }
        } elseif (Tools::isSubmit('submitBulkdelete'.$this->table)) {
            foreach (Tools::getValue($this->table.'Box') as $selection) {
                $order_state = new OrderState((int)$selection, $this->context->language->id);
                if (!$order_state->isRemovable()) {
                    $this->errors[] = $this->trans('For security reasons, you cannot delete default order statuses.', array(), 'Admin.Shopparameters.Notification');
                    break;
                }
            }

            if (!count($this->errors)) {
                return AdminController::postProcess();
            }
        } else {
            return AdminController::postProcess();
        }
    }

}