<?php

namespace PrestaShop\Module\Ps_facebook\Tracker;

use Context;
use PrestaShop\Module\Ps_facebook\Environment\Env;
use PrestaShop\Module\Ps_facebook\Environment\SegmentEnv;

class Segment implements TrackerInterface
{
    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var SegmentEnv
     */
    private $segmentEnv;

    /**
     * @var Context
     */
    private $context;

    /**
     * Segment constructor.
     *
     * @param SegmentEnv $segmentEnv
     */
    public function __construct(SegmentEnv $segmentEnv, Context $context)
    {
        $this->segmentEnv = $segmentEnv;
        $this->context = $context;
        $this->init();
    }

    /**
     * Init segment client with the api key
     */
    private function init()
    {
        \Segment::init($this->segmentEnv->getSegmentApiKey());
    }

    /**
     * Track event on segment
     *
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public function track()
    {
        if (empty($this->message)) {
            throw new \PrestaShopException('Message cannot be empty. Need to set it with setMessage() method.');
        }

        // Dispatch track depending on context shop
        $this->dispatchTrack();

        return true;
    }

    private function segmentTrack($userId)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        $referer = $_SERVER['HTTP_REFERER'];
        $queryString = '?' . $_SERVER['QUERY_STRING'];
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        \Segment::track([
            'userId' => $userId,
            'event' => $this->message,
            'channel' => 'browser',
            'context' => [
                'ip' => $ip,
                'userAgent' => $userAgent,
                'locale' => $this->context->language->iso_code,
                'page' => [
                    'referrer' => $referer,
                    'url' => $url,
                ],
            ],
            'properties' => array_merge([
                'module' => Env::MODULE_NAME,
            ], $this->options),
        ]);

        \Segment::flush();
    }

    /**
     * Handle tracking differently depending on the shop context
     *
     * @return mixed
     */
    private function dispatchTrack()
    {
        $dictionary = [
            \Shop::CONTEXT_SHOP => function () {
                return $this->trackShop();
            },
            \Shop::CONTEXT_GROUP => function () {
                return $this->trackShopGroup();
            },
            \Shop::CONTEXT_ALL => function () {
                return $this->trackAllShops();
            },
        ];

        return call_user_func($dictionary[$this->context->shop->getContext()]);
    }

    /**
     * Send track segment only for the current shop
     */
    private function trackShop()
    {
        $userId = $this->context->shop->domain;

        $this->segmentTrack($userId);
    }

    /**
     * Send track segment for each shop in the current shop group
     */
    private function trackShopGroup()
    {
        $shops = $this->context->shop->getShops(true, $this->context->shop->getContextShopGroupID());
        foreach ($shops as $shop) {
            $this->segmentTrack($shop['domain']);
        }
    }

    /**
     * Send track segment for all shops
     */
    private function trackAllShops()
    {
        $shops = $this->context->shop->getShops();
        foreach ($shops as $shop) {
            $this->segmentTrack($shop['domain']);
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
