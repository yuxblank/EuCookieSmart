<?php
/**
 * NOTICE OF LICENSE
 *
 * EuCookieSmart is a module for display a cookie law banner.
 * Copyright (C) 2017 Yuri Blanc
 * Email: yuxblank@gmail.com
 * Website: www.yuriblanc.it
 *
 * This program is distributed WITHOUT ANY WARRANTY
 * @license GNU General Public License v3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EuCookieSmart extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'EuCookieSmart';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'Yuri Blanc';
        //$this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->displayName = $this->l('EuCookieSmart');
        $this->description = $this->l('Cookie law banner with ajax features');
        $this->confirmUninstall = $this->l('Are you sure to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        parent::__construct();
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return
            Configuration::updateValue('EUCOOKIESMART_BUTTON_ACCEPT', true) &&
            Configuration::updateValue('EUCOOKIESMART_BUTTON_DECLINE', true) &&
            Configuration::updateValue('EUCOOKIESMART_BUTTON_POLICY', true) &&
            Configuration::updateValue('EUCOOKIESMART_BUTTON_POLICY_ARTICLE', null) &&
            Configuration::updateValue('EUCOOKIESMART_ACCEPT_CONTINUE', false) &&
            Configuration::updateValue('EUCOOKIESMART_ACCEPT_SCROLL', false) &&
            Configuration::updateValue('EUCOOKIESMART_RENEW_VISIT', true) &&
            Configuration::updateValue('EUCOOKIESMART_FIXED', false) &&
            Configuration::updateValue('EUCOOKIESMART_BOTTOM', false) &&
            Configuration::updateValue('EUCOOKIESMART_EFFECT', 'slide') &&
            Configuration::updateValue('EUCOOKIESMART_EXPIRE_DAYS', 30) &&
            Configuration::updateValue('EUCOOKIESMART_STYLE', "default.css") &&
            parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayFooter');
    }


    public function uninstall()
    {
        $bool = true;
        foreach ($this->getConfigFormValues() as $key => $val) {
            $bool = Configuration::deleteByName($key);
        }
        $bool &= $this->unregisterHook('backOfficeHeader') &&
            $this->unregisterHook('displayHeader') &&
            $this->unregisterHook('actionFrontControllerSetMedia') &&
            $this->unregisterHook('displayFooter');

        return $bool && parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitEuCookieSmartModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperFormCore();
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->default_form_language = $language->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEuCookieSmartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->override_folder = '/';
        $languages = Language::getLanguages(false);
        $isMultiLang = count($languages) > 1;

        $helper->tpl_vars = array(
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }


    private function isMultiLang()
    {
        $languages = Language::getLanguages(false);
        return count($languages) > 1;
    }


    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display accept button'),
                        'name' => 'EUCOOKIESMART_BUTTON_ACCEPT',
                        'is_bool' => true,
                        'desc' => $this->l('Show or not show accept button'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_BUTTON_ACCEPT_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_BUTTON_ACCEPT_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display decline button'),
                        'name' => 'EUCOOKIESMART_BUTTON_DECLINE',
                        'is_bool' => true,
                        'desc' => $this->l('Show or not show decline button'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_BUTTON_DECLINE_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_BUTTON_DECLINE_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display policy button'),
                        'name' => 'EUCOOKIESMART_BUTTON_POLICY',
                        'is_bool' => true,
                        'desc' => $this->l('Show or not show policy button'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_BUTTON_POLICY_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_BUTTON_POLICY_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-gears"></i>',
                        'desc' => $this->l('Enter the id of the article'),
                        'name' => 'EUCOOKIESMART_BUTTON_POLICY_ARTICLE',
                        'label' => $this->l('Policy CMS article'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Accept on continue'),
                        'name' => 'EUCOOKIESMART_ACCEPT_CONTINUE',
                        'is_bool' => true,
                        'desc' => $this->l('Show or not show policy button'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_ACCEPT_CONTINUE_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_ACCEPT_CONTINUE_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Accept on scroll'),
                        'name' => 'EUCOOKIESMART_ACCEPT_SCROLL',
                        'is_bool' => true,
                        'desc' => $this->l('Show or not show policy button'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_ACCEPT_SCROLL_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_ACCEPT_SCROLL_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Accept on scroll'),
                        'name' => 'EUCOOKIESMART_RENEW_VISIT',
                        'is_bool' => true,
                        'desc' => $this->l('Renew each visits'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_RENEW_VISIT_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_RENEW_VISIT_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Fixed position'),
                        'name' => 'EUCOOKIESMART_FIXED',
                        'is_bool' => true,
                        'desc' => $this->l('Use fixed position so the module will follow user scroll'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_FIXED_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_FIXED_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Bottom'),
                        'name' => 'EUCOOKIESMART_BOTTOM',
                        'is_bool' => true,
                        'desc' => $this->l('Display the module on the bottom of the page'),
                        'values' => array(
                            array(
                                'id' => 'EUCOOKIESMART_BOTTOM_ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'EUCOOKIESMART_BOTTOM_OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    array(
                        'type' => 'select',                              // This is a <select> tag.
                        'label' => $this->l('Effect'),         // The <label> for this <select> tag.
                        'desc' => $this->l('Choose an effect'),  // A help text, displayed right next to the <select> tag.
                        'name' => 'EUCOOKIESMART_EFFECT',                     // The content of the 'id' attribute of the <select> tag.
                        'required' => true,                              // If set to true, this option must be set.
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 'slide',       // The value of the 'value' attribute of the <option> tag.
                                    'name' => 'Slide'    // The value of the text content of the  <option> tag.
                                ),
                                array(
                                    'id_option' => 'fade',
                                    'name' => 'Fade'
                                ),
                                array(
                                    'id_option' => 'hide',
                                    'name' => 'Hide'
                                ),
                            ),                                              // $options contains the data itself.
                            'id' => 'id_option',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                            'name' => 'name'                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                        )
                    ),

                    array(
                        'type' => 'select',                              // This is a <select> tag.
                        'label' => $this->l('Style'),         // The <label> for this <select> tag.
                        'desc' => $this->l('Choose a style'),  // A help text, displayed right next to the <select> tag.
                        'name' => 'EUCOOKIESMART_STYLE',                     // The content of the 'id' attribute of the <select> tag.
                        'required' => true,                              // If set to true, this option must be set.
                        'options' => array(
                            'query' => $this->getStyleList(),                                              // $options contains the data itself.
                            'id' => 'id_option',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                            'name' => 'name'                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                        )
                    ),

                    array(
                        'col' => 2,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-calendar"></i>',
                        'desc' => $this->l('Set when the confirmation will be considered expired'),
                        'name' => 'EUCOOKIESMART_EXPIRE_DAYS',
                        'label' => $this->l('Expire in days'),
                    ),

                    // endfields
                ),

                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'saveConfig'
                ),
            ),
        );
    }


    private function getStyleList()
    {
        $list = array();
        $files = glob(__DIR__ . '/views/css/styles/' . '*.css');
        foreach ($files as $file) {
            $list[] = ["id_option" => basename($file), "name" => basename($file, ".css")];
        }
        return $list;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();
        return array(
            'EUCOOKIESMART_BUTTON_ACCEPT' => Configuration::get('EUCOOKIESMART_BUTTON_ACCEPT', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_BUTTON_DECLINE' => Configuration::get('EUCOOKIESMART_BUTTON_DECLINE', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_BUTTON_POLICY' => Configuration::get('EUCOOKIESMART_BUTTON_POLICY', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_BUTTON_POLICY_ARTICLE' => Configuration::get('EUCOOKIESMART_BUTTON_POLICY_ARTICLE', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_ACCEPT_CONTINUE' => Configuration::get('EUCOOKIESMART_ACCEPT_CONTINUE', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_ACCEPT_SCROLL' => Configuration::get('EUCOOKIESMART_ACCEPT_SCROLL', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_RENEW_VISIT' => Configuration::get('EUCOOKIESMART_RENEW_VISIT', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_FIXED' => Configuration::get('EUCOOKIESMART_FIXED', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_BOTTOM' => Configuration::get('EUCOOKIESMART_BOTTOM', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_EFFECT' => Configuration::get('EUCOOKIESMART_EFFECT', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_EXPIRE_DAYS' => Configuration::get('EUCOOKIESMART_EXPIRE_DAYS', null, $id_shop_group, $id_shop),
            'EUCOOKIESMART_STYLE' => Configuration::get('EUCOOKIESMART_STYLE', null, $id_shop_group, $id_shop)
        );

    }



    /**
     * Save form data.
     */
    protected function postProcess()
    {

        if (Tools::isSubmit('saveConfig')) {

            foreach (array_keys($this->getConfigFormValues()) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }

    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }


    public function hookDisplayHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/euCookieSmart.js');
        $cssFile = Configuration::get("EUCOOKIESMART_STYLE");
        $this->context->controller->addCSS($this->_path . 'views/css/styles/' . ($cssFile ? $cssFile : 'default.css'));
    }

    public function hookActionFrontControllerSetMedia()
    {
        /*        $this->context->controller->registerJavascript(
                    'module-euCookieSmart-euCookieSmart',
                    'modules/'.$this->name.'view/js/euCookieSmart.js',
                    [
                        'position' => 'head',
                        'inline' => true,
                        'priority' => 1000,
                    ]
                );*/

    }

    public function HookDisplayFooter()
    {
        $this->context->smarty->assign($this->getConfigFormValues());
        $this->context->smarty->assign($this->getText());
        $this->context->smarty->assign("policyCMSlink",$this->context->link->getCMSLink($this->getConfigFormValues()['EUCOOKIESMART_BUTTON_POLICY_ARTICLE']));
        return $this->display(__FILE__, 'euCookieSmart.tpl');
    }

    public function getText()
    {
        return array(
            'cookiePolicy' => $this->l('Read cookies policy', $this->name),
            'cookieMsg' => $this->l('This site uses cookies required for correct operation. By closing this banner, scrolling the page, clicking on a link or continuing navigation in any other way, you consent to the use of cookies.', $this->name),
            'cookieAcceptText' => $this->l('I Understand', $this->name),
            'cookieDeclineText' => $this->l('Disable cookies', $this->name),
            'cookiePolicyText' => $this->l('Privacy policy', $this->name)
        );
    }

}
