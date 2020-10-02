<?php
/*
 * 2007-2020 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\Ps_facebook\Translations;

class PsFacebookTranslations
{
    /**
     * @var \Module
     */
    private $module;

    /**
     * __construct
     *
     * @param \Module $module
     *
     * @return void
     */
    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    /**
     * Create all translations for Dashboard App
     *
     * @return array
     */
    public function getTranslations()
    {
        $locale = \Context::getContext()->language->iso_code;

        $translations[$locale] = [
            'general' => [
                'tabs' => [
                    'configuration' => $this->module->l('Configuration', 'PsFacebookTranslations'),
                    'help' => $this->module->l('Help', 'PsFacebookTranslations'),
                ],
            ],
            'configuration' => [
                'introduction' => [
                    'getStarted' => $this->module->l('ConfigurationIntroductionGetStarted', 'PsFacebookTranslations'),
                    'subTitle' => $this->module->l('ConfigurationIntroductionSubTitle', 'PsFacebookTranslations'),
                    'proPoints' => $this->module->l('ConfigurationIntroductionProPoints', 'PsFacebookTranslations'),
                    'resume' => $this->module->l('ConfigurationIntroductionResume', 'PsFacebookTranslations'),
                    'proPoint1Title' => $this->module->l('ConfigurationIntroductionProPoint1Title', 'PsFacebookTranslations'),
                    'proPoint1Description' => $this->module->l('ConfigurationIntroductionProPoint1Description', 'PsFacebookTranslations'),
                    'proPoint2Title' => $this->module->l('ConfigurationIntroductionProPoint2Title', 'PsFacebookTranslations'),
                    'proPoint2Description' => $this->module->l('ConfigurationIntroductionProPoint2Description', 'PsFacebookTranslations'),
                    'proPoint3Title' => $this->module->l('ConfigurationIntroductionProPoint3Title', 'PsFacebookTranslations'),
                    'proPoint3Description' => $this->module->l('ConfigurationIntroductionProPoint3Description', 'PsFacebookTranslations'),
                    'proPoint4Title' => $this->module->l('ConfigurationIntroductionProPoint4Title', 'PsFacebookTranslations'),
                    'proPoint4Description' => $this->module->l('ConfigurationIntroductionProPoint4Description', 'PsFacebookTranslations'),
                    'proPoint5Title' => $this->module->l('ConfigurationIntroductionProPoint5Title', 'PsFacebookTranslations'),
                    'proPoint5Description' => $this->module->l('ConfigurationIntroductionProPoint5Description', 'PsFacebookTranslations'),
                    'needMoreFeatures' => $this->module->l('ConfigurationIntroductionNeedMoreFeatures', 'PsFacebookTranslations'),
                    'seeDetailedPlans' => $this->module->l('ConfigurationIntroductionSeeDetailedPlans', 'PsFacebookTranslations'),
                ],
                'messages' => [
                    'success' => $this->module->l('ConfigurationMessagesSuccess', 'PsFacebookTranslations'),
                    'syncCatalogAdvice' => $this->module->l('ConfigurationMessagesSyncCatalogAdvice', 'PsFacebookTranslations'),
                    'syncCatalogButton' => $this->module->l('ConfigurationMessagesSyncCatalogButton', 'PsFacebookTranslations'),
                ],
                'facebook' => [
                    'title' => $this->module->l('ConfigurationFacebookTitle', 'PsFacebookTranslations'),
                    'notConnected' => [
                        'intro' => $this->module->l('ConfigurationFacebookNotConnectedIntro', 'PsFacebookTranslations'),
                        'connectButton' => $this->module->l('ConfigurationFacebookNotConnectedConnectButton', 'PsFacebookTranslations'),
                        'description' => $this->module->l('ConfigurationFacebookNotConnectedDescription', 'PsFacebookTranslations'),
                        'details' => $this->module->l('ConfigurationFacebookNotConnectedDetails', 'PsFacebookTranslations'),
                    ],
                    'connected' => [
                        'description' => $this->module->l('ConfigurationFacebookConnectedDescription', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('ConfigurationFacebookConnectedEditButton', 'PsFacebookTranslations'),
                        'facebookBusinessManager' => $this->module->l('ConfigurationFacebookConnectedFacebookBusinessManager', 'PsFacebookTranslations'),
                        'facebookBusinessManagerTooltip' => $this->module->l('ConfigurationFacebookConnectedFacebookBusinessManagerTooltip', 'PsFacebookTranslations'),
                        'facebookPixel' => $this->module->l('ConfigurationFacebookConnectedFacebookPixel', 'PsFacebookTranslations'),
                        'facebookPixelTooltip' => $this->module->l('ConfigurationFacebookConnectedFacebookPixelTooltip', 'PsFacebookTranslations'),
                        'facebookPage' => $this->module->l('ConfigurationFacebookConnectedFacebookPage', 'PsFacebookTranslations'),
                        'facebookPageTooltip' => $this->module->l('ConfigurationFacebookConnectedFacebookPageTooltip', 'PsFacebookTranslations'),
                        'facebookAds' => $this->module->l('ConfigurationFacebookConnectedFacebookAds', 'PsFacebookTranslations'),
                        'facebookAdsTooltip' => $this->module->l('ConfigurationFacebookConnectedFacebookAdsTooltip', 'PsFacebookTranslations'),
                    ],
                ],
                'app' => [
                    'like' => $this->module->l('ConfigurationAppLike', 'PsFacebookTranslations'),
                    'likes' => $this->module->l('ConfigurationAppLikes', 'PsFacebookTranslations'),
                    'createdAt' => $this->module->l('ConfigurationAppCreatedAt', 'PsFacebookTranslations'),
                    'lastActive' => $this->module->l('ConfigurationAppLastActive', 'PsFacebookTranslations'),
                    'activated' => $this->module->l('ConfigurationAppActivated', 'PsFacebookTranslations'),
                    'disabled' => $this->module->l('ConfigurationAppDisabled', 'PsFacebookTranslations'),
                ],
            ],
        ];

        return $translations;
    }
}
