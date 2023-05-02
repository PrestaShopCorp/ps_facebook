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

namespace PrestaShop\Module\PrestashopFacebook\Manager;

use PrestaShop\Module\PrestashopFacebook\Adapter\ConfigurationAdapter;
use PrestaShop\Module\PrestashopFacebook\API\Client\FacebookClient;
use PrestaShop\Module\PrestashopFacebook\Config\Config;
use stdClass;

class FbeFeatureManager
{
    /**
     * @var ConfigurationAdapter
     */
    private $configurationAdapter;
    /**
     * @var FacebookClient
     */
    private $facebookClient;

    public function __construct(ConfigurationAdapter $configurationAdapter, FacebookClient $facebookClient)
    {
        $this->configurationAdapter = $configurationAdapter;
        $this->facebookClient = $facebookClient;
    }

    /**
     * @param string $featureName
     * @param bool $state
     *
     * @return array|false
     */
    public function updateFeature($featureName, $state)
    {
        $featureConfiguration = $this->configurationAdapter->get(Config::FBE_FEATURE_CONFIGURATION . $featureName);
        $externalBusinessId = $this->configurationAdapter->get(Config::PS_FACEBOOK_EXTERNAL_BUSINESS_ID);

        if (!$featureConfiguration && !in_array($featureName, Config::CONFIGURABLE_FBE_FEATURES)) {
            return false;
        }

        $featureConfiguration = json_decode($featureConfiguration);
        if ($featureConfiguration === null) {
            $featureConfiguration = new stdClass();
        }

        if ($featureName == 'messenger_chat') {
            unset($featureConfiguration->default_locale);

            /* @see https://developers.facebook.com/docs/facebook-business-extension/fbe/reference#FBEMessengerChatConfigData */
            $featureConfiguration->domains = [
                \Tools::getShopDomainSsl(true),
            ];
        }

        $featureConfiguration->enabled = (bool) $state;
        $this->configurationAdapter->updateValue(Config::FBE_FEATURE_CONFIGURATION . $featureName, json_encode($featureConfiguration));

        $configuration = [
            $featureName => $featureConfiguration,
        ];

        return $this->facebookClient->updateFeature($externalBusinessId, $configuration);
    }
}
