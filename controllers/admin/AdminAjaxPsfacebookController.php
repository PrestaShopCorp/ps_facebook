<?php
/*
* 2007-2020 PrestaShop.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2020 PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*/

class AdminAjaxPsfacebookController extends ModuleAdminController
{
    public function postProcess()
    {
        return parent::postProcess();
    }

    public function ajaxProcessSaveTokenFbeAccount()
    {
        $token = \Tools::getValue('accessToken');
        $response = Configuration::updateValue('fbe_access_token', $token);

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessFacebookWebhooks()
    {
        // TODO: add some checks
        Configuration::updateValue('fbe_pixel_id', \Tools::getValue('pixel_id'));
        Configuration::updateValue('fbe_business_id', \Tools::getValue('business_id'));
        Configuration::updateValue('fbe_business_manager_id', \Tools::getValue('business_manager_id'));
        Configuration::updateValue('fbe_access_token', \Tools::getValue('access_token'));
        Configuration::updateValue('fbe_profiles', \Tools::getValue('profiles'));
        Configuration::updateValue('fbe_pages', \Tools::getValue('pages'));
        Configuration::updateValue('fbe_ad_account_id', \Tools::getValue('ad_account_id'));
        Configuration::updateValue('fbe_catalog_id', \Tools::getValue('catalog_id'));

    }
}
