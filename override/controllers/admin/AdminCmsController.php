<?php

class AdminCmsController extends AdminCmsControllerCore {

	public function __construct() {

        $this->bootstrap = true;
        $this->table = 'cms';
        $this->list_id = 'cms';
        $this->className = 'CMS';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_orderBy = 'position';

        AdminController::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );
        $this->fields_list = array(
            'id_cms' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'link_rewrite' => array('title' => $this->trans('URL', array(), 'Admin.Global')),
            'meta_title' => array('title' => $this->trans('Title', array(), 'Admin.Global'), 'filter_key' => 'b!meta_title'),
            'position' => array('title' => $this->trans('Position', array(), 'Admin.Global'),'filter_key' => 'position', 'align' => 'center', 'class' => 'fixed-width-sm', 'position' => 'position'),
            'active' => array('title' => $this->trans('Displayed', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => false),
            'display_raw' => array('title' => $this->trans('Contenu uniquement', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status', 'class' => 'fixed-width-sm', 'type' => 'bool', 'orderby' => false)
        );

        // The controller can't be call directly
        // In this case, AdminCmsContentController::getCurrentCMSCategory() is null
        if (!AdminCmsContentController::getCurrentCMSCategory()) {
            $this->redirect_after = '?controller=AdminCmsContent&token='.Tools::getAdminTokenLite('AdminCmsContent');
            $this->redirect();
        }

        $this->_category = AdminCmsContentController::getCurrentCMSCategory();
        $this->tpl_list_vars['icon'] = 'icon-folder-close';
        $this->tpl_list_vars['title'] = $this->trans('Pages in category "%name%"', array('%name%' => $this->_category->name[Context::getContext()->employee->id_lang]), 'Admin.Design.Feature');
        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'cms_category` c ON (c.`id_cms_category` = a.`id_cms_category`)';
        $this->_select = 'a.position ';
        $this->_where = ' AND c.id_cms_category = '.(int)$this->_category->id;
    }

	public function renderForm() {

        if (!$this->loadObject(true)) {
            return;
        }

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }

        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $categories = CMSCategory::getCategories($this->context->language->id, false);
        $html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_cms_category'), 1);

		$this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Page'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                // custom template
                array(
                    'type' => 'select_category',
                    'label' => $this->trans('Page Category', array(), 'Admin.Design.Feature'),
                    'name' => 'id_cms_category',
                    'options' => array(
                        'html' => $html_categories,
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta title', array(), 'Admin.Global'),
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta description', array(), 'Admin.Global'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->trans('Meta keywords', array(), 'Admin.Global'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => array(
                        $this->trans('To add "tags" click in the field, write something, and then press "Enter."', array(), 'Admin.Design.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Friendly URL', array(), 'Admin.Global'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->trans('Only letters and the hyphen (-) character are allowed.', array(), 'Admin.Design.Feature')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Page content', array(), 'Admin.Design.Feature'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' <>;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Indexation by search engines', array(), 'Admin.Design.Feature'),
                    'name' => 'indexation',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'indexation_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'indexation_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Displayed', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
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
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Affichage nu', array(), 'Admin.Global'),
                    'name' => 'display_raw',
                    'required' => false,
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
                    'hint' => $this->trans('Afficher sans header ni footer', array(), 'Admin.Notifications.Info')
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
            'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'viewcms',
                    'type' => 'submit',
                    'title' => $this->trans('Save and preview', array(), 'Admin.Actions'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-preview'
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

        if (Validate::isLoadedObject($this->object)) {
            $this->context->smarty->assign('url_prev', $this->getPreviewUrl($this->object));
        }

        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );
        return AdminController::renderForm();
    }

}