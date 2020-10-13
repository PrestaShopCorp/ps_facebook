<?php

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use Configuration;
use PrestaShop\Module\PrestashopFacebook\Database\Config;

class MessengerHandler
{
    /**
     * @var int
     */
    private $pageId;

    public function __construct()
    {
        $pageList = explode(',', Configuration::get('PS_FACEBOOK_PAGES'));
        $this->pageId = (int) reset($pageList);
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
            'ps_facebook_messenger_app_id' => Config::APP_ID,
            'ps_facebook_messenger_page_id' => $this->pageId,
        ];
    }
}
