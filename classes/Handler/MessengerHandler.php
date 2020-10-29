<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Configuration;
use Language;
use PrestaShop\Module\PrestashopFacebook\Config\Config;

class MessengerHandler
{
    /**
     * @var int
     */
    private $pageId;

    /**
     * @var Language
     */
    private $lang;

    public function __construct(Language $lang)
    {
        $pageList = explode(',', Configuration::get('PS_FACEBOOK_PAGES'));
        $this->pageId = (int) reset($pageList);
        $this->lang = $lang;
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        return !empty($this->pageId);
    }

    public function handle()
    {
        return [
            'ps_facebook_messenger_api_version' => Config::API_VERSION,
            'ps_facebook_messenger_app_id' => $_ENV['PSX_FACEBOOK_APP_ID'],
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
