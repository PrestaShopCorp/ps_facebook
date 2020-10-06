<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Facebook\Facebook;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Exception;

class FacebookDataProvider
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * FacebookDataProvider constructor.
     * @param string $appId
     * @param string $appSecret
     * @param string $accessToken
     */
    public function __construct($appId, $appSecret, $accessToken)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
    }

    /**
     * https://github.com/facebookarchive/php-graph-sdk
     *
     * @return \Facebook\FacebookResponse
     */
    public function getContext()
    {
        try {
            $fb = new Facebook([
                'app_id' => $this->appId,
                'app_secret' => $this->appSecret
            ]);
            // Returns a `FacebookFacebookResponse` object
            $response = $fb->get("/{$this->appId}", $this->accessToken);

        } catch (\Exception $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Exception $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        return $response;
    }
}
