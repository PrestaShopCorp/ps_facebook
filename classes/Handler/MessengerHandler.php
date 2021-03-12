<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Language;
use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Config\Env;

class MessengerHandler
{
    /**
     * @var string
     */
    private $pageId;

    /**
     * @var Language
     */
    private $lang;

    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;

    /**
     * @var Env
     */
    private $env;

    public function __construct(
        Language $lang,
        ConfigurationAdapter $configurationAdapter,
        Env $env
    ) {
        $pageList = explode(',', $configurationAdapter->get('PS_FACEBOOK_PAGES'));
        $this->pageId = reset($pageList);
        $this->lang = $lang;
        $this->configurationAdapter = $configurationAdapter;
        $this->env = $env;
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        if (empty($this->pageId)) {
            return false;
        }

        $messengerChatFeature = json_decode($this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . 'messenger_chat'));

        return $messengerChatFeature && $messengerChatFeature->enabled;
    }

    public function handle()
    {
        return [
            'ps_facebook_messenger_api_version' => Config::API_VERSION,
            'ps_facebook_messenger_app_id' => $this->env->get('PSX_FACEBOOK_APP_ID'),
            'ps_facebook_messenger_page_id' => $this->pageId,
            'ps_facebook_messenger_locale' => $this->getLocale(),
        ];
    }

    /**
     * Return the current language locale so the messenger is properly localized
     *
     * @return string
     */
    private function getLocale()
    {
        // PrestaShop 1.7+
        if (!empty($this->lang->locale)) {
            return str_replace('-', '_', $this->lang->locale);
        }

        return 'en_US';
    }
}
