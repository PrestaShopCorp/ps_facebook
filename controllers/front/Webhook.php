<?php
/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class ps_facebookWebhookModuleFrontController extends ModuleFrontController
{
    public function display()
    {
        // TODO: AFAIK all of this should be done by another service

        // verifyToken should be set in dashboard app and in config of shop i guess
        // anyway they must match
        // if (!Configuration::get('fbe_verify_token') === \Tools::getValue('verify_token')) {
        //     $this->ajaxDie('wrong verify token');
        //     // add log about it
        // }

        // if (!\Tools::getValue('field') == 'fbe_install') {
        //     $this->ajaxDie('unsupported event');
        // }
        // $values = json_decode(\Tools::getValue('value'), true);
        $jsonGet = json_encode($_GET);
        $jsonPost = json_encode($_POST);
        file_put_contents('modules/ps_facebook/debug.log', '');
        file_put_contents('modules/ps_facebook/debug.log', $jsonGet . PHP_EOL, FILE_APPEND);
        file_put_contents('modules/ps_facebook/debug.log', $jsonPost . PHP_EOL, FILE_APPEND);

        // TODO: add some checks
        // Configuration::updateValue('fbe_pixel_id', $values['pixel_id']);
        // Configuration::updateValue('fbe_business_id', $values['business_id']);
        // Configuration::updateValue('fbe_business_manager_id', $values['business_manager_id']);
        // if (!empty($values['access_token'])) {
        //     Configuration::updateValue('fbe_access_token', $values['access_token']);
        // }
        // Configuration::updateValue('fbe_profiles', $values['profiles']); is an array
        // Configuration::updateValue('fbe_pages', $values['pages']); is an array
        // Configuration::updateValue('fbe_ad_account_id', $values['ad_account_id']);
        // Configuration::updateValue('fbe_catalog_id', $values['catalog_id']);

        // hub.challenge is sent by FB and must be returned in response
        $this->ajaxDie(Tools::getValue('hub_challenge'));
    }
}
