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

namespace PrestaShop\Module\PrestashopFacebook\DTO;

use JsonSerializable;

class ConfigurationData implements JsonSerializable
{
    /**
     * @var array
     */
    private $contextPsAccounts;

    /**
     * @var ContextPsFacebook
     */
    private $contextPsFacebook;

    /**
     * @var string
     */
    private $psFacebookExternalBusinessId;

    /**
     * @var string
     */
    private $psAccountsToken;

    /**
     * @var string
     */
    private $psFacebookCurrency;

    /**
     * @var string
     */
    private $psFacebookTimezone;

    /**
     * @var string
     */
    private $psFacebookLocale;

    /**
     * @var string
     */
    private $psFacebookPixelActivationRoute;

    /**
     * @var string
     */
    private $psFacebookFbeOnboardingSaveRoute;

    /**
     * @var string
     */
    private $psFacebookFbeUiUrl;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var string
     */
    private $languageCode;

    /**
     * @return array
     */
    public function getContextPsAccounts()
    {
        return $this->contextPsAccounts;
    }

    /**
     * @param array $contextPsAccounts
     *
     * @return ConfigurationData
     */
    public function setContextPsAccounts($contextPsAccounts)
    {
        $this->contextPsAccounts = $contextPsAccounts;

        return $this;
    }

    /**
     * @return ContextPsFacebook
     */
    public function getContextPsFacebook()
    {
        return $this->contextPsFacebook;
    }

    /**
     * @param ContextPsFacebook $contextPsFacebook
     *
     * @return ConfigurationData
     */
    public function setContextPsFacebook($contextPsFacebook)
    {
        $this->contextPsFacebook = $contextPsFacebook;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookExternalBusinessId()
    {
        return $this->psFacebookExternalBusinessId;
    }

    /**
     * @param string $psFacebookExternalBusinessId
     *
     * @return ConfigurationData
     */
    public function setPsFacebookExternalBusinessId($psFacebookExternalBusinessId)
    {
        $this->psFacebookExternalBusinessId = $psFacebookExternalBusinessId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsAccountsToken()
    {
        return $this->psAccountsToken;
    }

    /**
     * @param string $psAccountsToken
     *
     * @return ConfigurationData
     */
    public function setPsAccountsToken($psAccountsToken)
    {
        $this->psAccountsToken = $psAccountsToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookCurrency()
    {
        return $this->psFacebookCurrency;
    }

    /**
     * @param string $psFacebookCurrency
     *
     * @return ConfigurationData
     */
    public function setPsFacebookCurrency($psFacebookCurrency)
    {
        $this->psFacebookCurrency = $psFacebookCurrency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookTimezone()
    {
        return $this->psFacebookTimezone;
    }

    /**
     * @param string $psFacebookTimezone
     *
     * @return ConfigurationData
     */
    public function setPsFacebookTimezone($psFacebookTimezone)
    {
        $this->psFacebookTimezone = $psFacebookTimezone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookLocale()
    {
        return $this->psFacebookLocale;
    }

    /**
     * @param string $psFacebookLocale
     *
     * @return ConfigurationData
     */
    public function setPsFacebookLocale($psFacebookLocale)
    {
        $this->psFacebookLocale = $psFacebookLocale;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookPixelActivationRoute()
    {
        return $this->psFacebookPixelActivationRoute;
    }

    /**
     * @param string $psFacebookPixelActivationRoute
     *
     * @return ConfigurationData
     */
    public function setPsFacebookPixelActivationRoute($psFacebookPixelActivationRoute)
    {
        $this->psFacebookPixelActivationRoute = $psFacebookPixelActivationRoute;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookFbeOnboardingSaveRoute()
    {
        return $this->psFacebookFbeOnboardingSaveRoute;
    }

    /**
     * @param string $psFacebookFbeOnboardingSaveRoute
     *
     * @return ConfigurationData
     */
    public function setPsFacebookFbeOnboardingSaveRoute($psFacebookFbeOnboardingSaveRoute)
    {
        $this->psFacebookFbeOnboardingSaveRoute = $psFacebookFbeOnboardingSaveRoute;

        return $this;
    }

    /**
     * @return string
     */
    public function getPsFacebookFbeUiUrl()
    {
        return $this->psFacebookFbeUiUrl;
    }

    /**
     * @param string $psFacebookFbeUiUrl
     *
     * @return ConfigurationData
     */
    public function setPsFacebookFbeUiUrl($psFacebookFbeUiUrl)
    {
        $this->psFacebookFbeUiUrl = $psFacebookFbeUiUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return ConfigurationData
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     *
     * @return ConfigurationData
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'contextPsAccounts' => $this->getContextPsAccounts(),
            'contextPsFacebook' => $this->getContextPsFacebook(),
            'psFacebookExternalBusinessId' => $this->getPsFacebookExternalBusinessId(),
            'psAccountsToken' => $this->getPsAccountsToken(),
            'psFacebookCurrency' => $this->getPsFacebookCurrency(),
            'psFacebookTimezone' => $this->getPsFacebookTimezone(),
            'psFacebookLocale' => $this->getPsFacebookLocale(),
            'psFacebookPixelActivationRoute' => $this->getPsFacebookPixelActivationRoute(),
            'psFacebookFbeOnboardingSaveRoute' => $this->getPsFacebookFbeOnboardingSaveRoute(),
            'psFacebookFbeUiUrl' => $this->getPsFacebookFbeUiUrl(),
            'i18nSettings' => [
                'isoCode' => $this->getIsoCode(),
                'languageLocale' => $this->getLanguageCode(),
            ],
        ];
    }
}
