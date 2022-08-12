<?php

class AdminTabtaskController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table     = 'news';
        $this->className = 'MyNews';
        $this->lang      = true;
        $this->bootstrap = true;

        parent::__construct();
        $this->fields_list = array(
            'id_news'     => array(
                'title' => $this->l('id_news'),
                'width' => 'auto',
                'type'  => 'text',
            ),

            'active'      => array(
                'title'  => $this->l('Status'),
                'width'  => 'auto',
                'type'   => 'bool',
                'active' => 'status',
            ),

            'tittle'      => array(
                'title' => $this->l('Tittle'),
                'width' => 'auto',
                'type'  => 'text',

            ),

            'description' => array(
                'title' => $this->l('Description'),
                'width' => 'auto',
                'type'  => 'text',

            ),

        );

        $this->fields_form = [
            'legend' => [
                'icon'  => 'icon-cogs',
                'title' => $this->l('Settings'),
            ],

            'input'  => [
                [
                    'type' => 'hidden',
                    'name' => 'id_news',
                ],

                [
                    'type'     => 'text',
                    'lang'     => true,
                    'label'    => $this->l('Tittle'),
                    'name'     => 'tittle',
                    'size'     => 20,
                    'required' => true,
                ],

                [
                    'type'         => 'textarea',
                    'lang'         => true,
                    'autoload_rte' => true,
                    'label'        => $this->trans('Description', [], 'Modules.Dataprivacy.Admin'),
                    'name'         => 'description',
                    'required'     => true,
                    'desc'         => $this->trans('Enter news description here', [], 'Modules.Dataprivacy.Admin')
                ],

                [
                    'type'     => 'switch',
                    'label'    => $this->l('Status'),
                    'name'     => 'active',
                    'is_bool'  => true,
                    'required' => true,
                    'values'   => [
                        [
                            'id'    => 1 . '_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global')
                        ],

                        [
                            'id'    => 2 . '_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global')
                        ],
                    ],
                ],
            ],

            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('details');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text'    => $this->l('Delete selected'),
                'confirm' => $this->l('Would you like to delete the selected items?'),
                'icon'    => 'icon-trash',
            ),
        );
    }
}
