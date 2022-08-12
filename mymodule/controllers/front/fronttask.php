<?php
 
class MyModuleFronttaskModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $value = Tools::getValue('id_news');
        $lang = $this->context->language->id;
        parent::initContent();
        $this->context->smarty->assign(array(
        'description' => MyNews::descriptionFrontoffice($lang, $value)
        ));

        $this->setTemplate('module:mymodule/views/templates/front/fronttask.tpl');
    }
}
