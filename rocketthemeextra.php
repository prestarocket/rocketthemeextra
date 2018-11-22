<?php
/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*
*  @author Prestarocket <prestarocket@gmail.com>
*  @copyright  2007-2018 Prestarocket
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;

class rocketthemeextra extends Module
{

    public function __construct()
    {
        $this->name = 'rocketthemeextra';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Prestarocket';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->ps_versions_compliancy = array('min' => '1.7.4.0', 'max' => _PS_VERSION_);

        $this->displayName = $this->l('Reset theme setting');
        $this->description = $this->l('Allows to reset theme setting and unhook modules with theme.yml');

        $this->_html = '';
        $this->errors = array();
        $this->id_lang = $this->context->language->id;
        $this->languages = Language::getLanguages(true);
    }

    public function install()
    {
        return parent::install();
    }


    public function uninstall()
    {
        return parent::uninstall();
    }


    public function getContent()
    {
        $this->postProcess();
        return $this->_html . $this->renderForm();
    }

    protected function postProcess()
    {
        if (Tools::isSubmit('ROCKET_RESET_THEME')) {
            $res = true;
            $this->resetThemeSettings();

            if (!$res) {
                $this->_html = $this->displayError($this->l('An error occured!'));
            } else {
                $this->_html = $this->displayConfirmation($this->l('Settings saved!'));
            }
        }
    }


    protected function resetThemeSettings()
    {
        $themeName = $this->context->shop->theme_name;

        $theme_manager = (new ThemeManagerBuilder($this->context, Db::getInstance()))->build();
        $theme_manager->reset($themeName);
        $toUnHook = $this->context->shop->theme->get('global_settings.modules.to_unhook', array());
        if (!$toUnHook) {
            return;
        }
        foreach ($toUnHook as $hook => $modules) {
            foreach ($modules as $module_name) {
                $module = Module::getInstanceByName($module_name);
                if ($module && $module->isRegisteredInHook($hook)) {
                    Hook::unregisterHook($module, $hook);
                }
            }
        }

    }

    protected function renderForm()
    {
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Reset Theme Settings'),
                'icon' => 'icon-cogs',
                'tinymce' => true,
            ),
            'input' => array(),
            'submit' => array(
                'title' => $this->l('Reset')
            )
        );

        foreach ($this->getConfigFields($this->id_lang) as $field_name => $field) {
            $fields_form[0]['form']['input'][] = $field;
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->default_form_language = $this->id_lang;
        $helper->allow_employee_form_lang = $this->id_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'ROCKET_RESET_THEME';

        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValue(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->id_lang
        );

        return $helper->generateForm($fields_form);
    }

    protected function getConfigFieldsValue()
    {
        return array();

    }

    private function getConfigFields($id_lang)
    {
        return array();
    }
}
