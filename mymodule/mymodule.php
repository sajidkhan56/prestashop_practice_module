<?php
 
if (!defined('_PS_VERSION_')) {
	exit;
}

include_once _PS_ROOT_DIR_ . '/modules/mymodule/classes/mynews.php';

class MyModule extends Module {
	public function __construct() {
		$this->name = 'mymodule';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'FMM Modules';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = [
			'min' => '1.6',
			'max' => '1.7.99',
		];
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('mymodule');
		$this->description = $this->l('Created for paractice');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		if (!Configuration::get('MYMODULE_NAME')) {
			$this->warning = $this->l('No name provided');
		}
	}

	public function install() {
		if (Shop::isFeatureActive()) {
			Shop::setContext(Shop::CONTEXT_ALL);
		}
		include_once $this->local_path . 'sql/install.php';
		return (parent::install() && $this->registerHook('displayHome') && $this->registerHook('header')
			&& Configuration::updateValue('MYMODULE_NAME', 'my friend') && $this->createTablink()
		);
	}

	public function uninstall() {
		include_once $this->local_path . 'sql/uninstall.php';
		Configuration::updateValue('MYMODULE_SETTINGS', serialize([true, true, false]));

		return (parent::uninstall() && Configuration::deleteByName('MYMODULE_NAME') && $this->uninstallTab());
	}

	public function uninstallTab() {
		$id_tab = (int) Tab::getIdFromClassName('AdminTabtask');
		$tab = new Tab($id_tab);
		return $tab->delete();
	}

	public function getContent() {
		$message = $this->initList();
		if (Tools::isSubmit('add' . $this->name) || Tools::isSubmit('update' . $this->name)) {
			return $this->displayForm();

		} elseif (Tools::isSubmit('submit' . $this->name)) {

			return $this->postProcess() . $this->initList();

		} elseif (Tools::isSubmit('delete' . $this->name)) {
			$id_news = Tools::getValue('id_news');
			$object = new MyNews($id_news);
			$object->delete();
		} elseif (Tools::isSubmit('submitBulkdelete' . $this->name)) {
			$checkboxvalue = Tools::getValue('mymoduleBox');
			$length = count($checkboxvalue);
			for ($i = 0; $i < $length; $i++) {
				$object = new MyNews($checkboxvalue[$i]);
			}
			$object->delete();
		}
		return $message;
	}

	public function createTablink() {
		$tab = new Tab();

		foreach (Language::getLanguages() as $lang) {
			$tab->name[$lang['id_lang']] = $this->l('News Tab');
		}
		$tab->class_name = 'AdminTabtaskParent';
		$tab->module = $this->name;
		$tab->id_parent = 0;
		if ($tab->add()) {

			$child = new Tab();

			foreach (Language::getLanguages() as $lang) {
				$child->name[$lang['id_lang']] = $this->l('News');
			}
			$child->class_name = 'AdminTabtask';
			$child->module = $this->name;
			$child->id_parent = $tab->id;
			$child->add();
		}
		return true;
	}

	public function postProcess() {
		$output = '';
		$text_tittle = [];
		$text_description = [];
		$id_news = Tools::getValue('id_news', 0);

		if ($id_news) {
			$object = new MyNews((int) $id_news);
		} else {
			$object = new MyNews();
		}

		$languages = Language::getLanguages(false);

		if (!Tools::getValue('tittle_' . (int) Configuration::get('PS_LANG_DEFAULT'), false)) {
			$output = $this->displayError($this->trans('Please fill out all fields.', [], 'Admin.Notifications.Error'));
			return $output;
		} else {
			foreach ($languages as $lang) {
				$text_tittle[$lang['id_lang']] = (string) Tools::getValue('tittle_' . $lang['id_lang']);
			}
		}

		if (!Tools::getValue('description_' . (int) Configuration::get('PS_LANG_DEFAULT'), false)) {
			$output = $this->displayError($this->trans('Please fill out all fields.', [], 'Admin.Notifications.Error'));
			return $output;
		} else {
			foreach ($languages as $lang) {
				$text_description[$lang['id_lang']] = (string) Tools::getValue('description_' . $lang['id_lang']);
			}
		}
		$switch_value = (int) Tools::getValue('active');
		$object->active = $switch_value;
		$object->tittle = $text_tittle;
		$object->description = $text_description;
		$object->save();

		$output = $this->displayConfirmation($this->l('Settings updated'));
		return $output;
	}

	public function displayForm() {

		$default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
		$form = [
			'form' => [
				'legend' => [
					'icon' => 'icon-cogs',
					'title' => $this->l('Settings'),
				],

				'input' => [
					[
						'type' => 'hidden',
						'name' => 'id_news',
					],
					[
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Tittle'),
						'name' => 'tittle',
						'size' => 20,
						'required' => true,
					],
					[
						'type' => 'textarea',
						'lang' => true,
						'autoload_rte' => true,
						'label' => $this->trans('Description', [], 'Modules.Dataprivacy.Admin'),
						'name' => 'description',
						'required' => true,
						'desc' => $this->trans('Enter news description here', [], 'Modules.Dataprivacy.Admin')
					],
					[
						'type' => 'switch',
						'label' => $this->l('Status'),
						'name' => 'active',
						'is_bool' => true,
						'required' => true,
						'values' => [
							[
								'id' => 1 . '_on',
								'value' => 1,
								'label' => $this->trans('Enabled', [], 'Admin.Global')
							],

							[
								'id' => 2 . '_off',
								'value' => 0,
								'label' => $this->trans('Disabled', [], 'Admin.Global')
							],
						],
					],

				],
				'buttons' => [
					[
						'title' => $this->l('cancel'),
						'href' => 'index.php?controller=AdminModules&configure=mymodule&token=9b882c8c88cefdd6fa99113f539cdd08',
						'js' => 'someFunction()',
						'class' => 'btn btn-default pull-left',
						'type' => 'button',
						'id' => 'mybutton',
						'name' => 'mybutton',
					],
				],
				'submit' => [
					'title' => $this->l('Save'),
					'class' => 'btn btn-default pull-right',
				],

			],
		];

		$helper = new HelperForm();
		$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

		// Module, token and currentIndex
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang =
		Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->name_controller = $this->name;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
		'&configure=' . $this->name .
		'&tab_module=' . $this->tab .
		'&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		foreach (Language::getLanguages(false) as $lang) {
			$helper->languages[] = [
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0),
			];
		}
		$helper->tpl_vars = [
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		];
		$helper->submit_action = 'submit' . $this->name;
		// $helper->show_cancel_button = true;
		$helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
		return $helper->generateForm([$form]);
	}

	public function getConfigFieldsValues() {
		$return = [];
		$return['id_news'] = 0;
		$return['active'] = 0;
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang) {
			$return['description'][(int) $lang['id_lang']] = '';
			$return['tittle'][(int) $lang['id_lang']] = '';
		}

		if (($id_news = Tools::getValue('id_news'))) {
			$news = new MyNews((int) $id_news);
			$return['id_news'] = $news->id;
			$return['active'] = $news->active;
			$return['description'] = $news->description;
			$return['tittle'] = $news->tittle;
		}
		return $return;
	}

	public function hookDisplayHome() {
		$lang = $this->context->language->id;

		$data = MyNews::tittleFrontoffice($lang);

		$this->context->smarty->assign(['id_news' => $data]);

		return $this->display(__FILE__, 'views/templates/hook/mymodule.tpl');
	}

	private function initList() {
		$lang = $this->context->language->id;
		$sql = MyNews::allRecords($lang);

		$this->fields_list = array(
			'id_news' => array(
				'title' => $this->l('id_news'),
				'width' => 'auto',
				'type' => 'text',
				'search' => false,
			),

			'active' => array(
				'title' => $this->l('Status'),
				'width' => 'auto',
				'type' => 'bool',
				'search' => false,
				'active' => 'status',

			),
			'tittle' => array(
				'title' => $this->l('Tittle'),
				'width' => 'auto',
				'type' => 'text',
				'search' => false,

			),
			'description' => array(
				'title' => $this->l('Description'),
				'width' => 'auto',
				'type' => 'text',
				'search' => false,

			),

		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = false;
		$helper->identifier = 'id_news';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = true;
		$helper->title = $this->displayName;
		$helper->table = $this->name;
		$helper->bulk_actions = true;
		$helper->force_show_bulk_actions = false;
		$helper->bulk_actions = [
			'delete' => [
				'text' => $this->trans('Delete selected', [], 'Admin.Notifications.Info'),
				'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Info'),
				'icon' => 'icon-trash',
			],

		];
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
		$helper->toolbar_btn['new'] = array(
			'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&add' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add'),
		);
		return $helper->generateList($sql, $this->fields_list);
	}
}
