<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use GuzzleHttp\Client;

class FacebookFbeDataProvider extends FacebookDataProvider
{
    /**
     * @var string
     */
    private $externalBusinessId;

    /**
     * @param int $appId
     * @param string $sdkVersion
     * @param string $accessToken
     * @param string $externalBusinessId
     */
    public function __construct($appId, $accessToken, $sdkVersion, $externalBusinessId)
    {
        parent::__construct($appId, $accessToken, $sdkVersion);
        $this->externalBusinessId = $externalBusinessId;
    }

    /**
     * https://developers.facebook.com/docs/marketing-api/fbe/fbe2/guides/get-features/#fbe-installation-api
     * 
     * This allow the module to retrieve all the assigned IDs during the FBE onboarding
     * 
     * @return array
     */
    public function getFbeInstallationContext()
    {
        $client = new Client();
        $response = $client->get(
            self::API_URL . '/' . $this->sdkVersion . '/fbe_business/fbe_installs',
            [
                'query' =>
                    [
                        'fbe_external_business_id' => $this->externalBusinessId,
                        'access_token' => $this->accessToken,
                    ]
            ]
        );

        if (!$response || !$response->getBody()) {
            return [];
        }

        $data = json_decode($response->getBody()->getContents(), true);
        return reset($data['data']);
    }
}