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
    /**
     * AJAX: Get the form Payload for PSX. Check the data and send it to PSL
     */
    public function ajaxProcessFbeAccount()
    {
        $payload = json_decode(\Tools::getValue('payload'), true);

        if (!empty($errors)) {
            $this->ajaxDie(json_encode($errors));
        }

        // Configuration::updateValue();

        $this->ajaxDie(json_encode($response));
    }
}
