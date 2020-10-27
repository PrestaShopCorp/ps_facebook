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
                    'configuration' => $this->module->l('Configure', 'PsFacebookTranslations'),
                    'catalog' => $this->module->l('Export product catalog', 'PsFacebookTranslations'),
                    'help' => $this->module->l('Help', 'PsFacebookTranslations'),
                    'integrate' => $this->module->l('Integrate', 'PsFacebookTranslations'),
                ],
            ],
            'configuration' => [
                'introduction' => [
                    'getStarted' => $this->module->l('Get started', 'PsFacebookTranslations'),
                    'subTitle' => $this->module->l('Build your business presence into Facebook community easily and quickly!', 'PsFacebookTranslations'),
                    'proPoints' => $this->module->l("- No credit card required \n- Easy setup \n- Cancel anytime", 'PsFacebookTranslations'),
                    'resume' => $this->module->l('Make your first steps with PrestaShop Facebook!', 'PsFacebookTranslations'),
                    'proPoint1Title' => $this->module->l('Manage your business', 'PsFacebookTranslations'),
                    'proPoint1Description' => $this->module->l("Control your ad settings and business tools \nfrom one place.", 'PsFacebookTranslations'),
                    'proPoint2Title' => $this->module->l('Manage your ad account', 'PsFacebookTranslations'),
                    'proPoint2Description' => $this->module->l('Choose the account where you store ad payment information for your business.', 'PsFacebookTranslations'),
                    'proPoint3Title' => $this->module->l('Understand your traffic', 'PsFacebookTranslations'),
                    'proPoint3Description' => $this->module->l('Use data from Facebook Pixel* to understand actions people take on your website.', 'PsFacebookTranslations'),
                    'proPoint4Title' => $this->module->l('Build and manage inventory', 'PsFacebookTranslations'),
                    'proPoint4Description' => $this->module->l("Manage your product catalog and \nunlock the power of ads.", 'PsFacebookTranslations'),
                    'proPoint5Title' => $this->module->l('Reach more people', 'PsFacebookTranslations'),
                    'proPoint5Description' => $this->module->l("Help people find your products on Instagram \nand Facebook.", 'PsFacebookTranslations'),
                    'needMoreFeatures' => $this->module->l('Need more features?', 'PsFacebookTranslations'),
                    'seeDetailedPlans' => $this->module->l('See detailed plans', 'PsFacebookTranslations'),
                ],
                'messages' => [
                    'success' => $this->module->l('PrestaShop Facebook is now activated!', 'PsFacebookTranslations'),
                    'syncCatalogAdvice' => $this->module->l('One more thing: Match your categories and import your product catalog to set up Facebook Shop, Instagram Shopping and create ad campaigns.', 'PsFacebookTranslations'),
                    'stepPsAccount' => $this->module->l('Connect your PrestaShop account', 'PsFacebookTranslations'),
                    'stepPsFacebook' => $this->module->l('Connect your store to Facebook', 'PsFacebookTranslations'),
                    'stepCategoryMatching' => $this->module->l('Match categories', 'PsFacebookTranslations'),
                    'stepProductSync' => $this->module->l('Import your product catalog', 'PsFacebookTranslations'),
                    'syncCatalogButton' => $this->module->l('Match categories', 'PsFacebookTranslations'),
                    'reloadButton' => $this->module->l('Reload', 'PsFacebookTranslations'),
                    'unknownOnboardingError' => $this->module->l('An unknown error occurred during onboarding process. Please reload and try again.', 'PsFacebookTranslations'),
                ],
                'facebook' => [
                    'title' => $this->module->l('Connect your store to Facebook', 'PsFacebookTranslations'),
                    'notConnected' => [
                        'intro' => $this->module->l('Integrate your shop with Facebook.', 'PsFacebookTranslations'),
                        'connectButton' => $this->module->l('Connect to Facebook', 'PsFacebookTranslations'),
                        'description' => $this->module->l('With Facebook Business Manager, Facebook Page, Facebook Ads account, Facebook Pixel, Instagram Business account and products, you’ll be able to:', 'PsFacebookTranslations'),
                        'details' => $this->module->l("- Create catalog \n- Sync your catalog automatically and in real time \n- Create Shop on Facebook Page & Instagram Shopping \n- Add Messenger plugin in your shop \n- Customize your Facebook Page with call-to-action", 'PsFacebookTranslations'),
                    ],
                    'connected' => [
                        'description' => $this->module->l('You authorize this Facebook account to connect to your store:', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Restart onboarding', 'PsFacebookTranslations'),
                        'facebookBusinessManager' => $this->module->l('Facebook Business Manager', 'PsFacebookTranslations'),
                        'facebookBusinessManagerTooltip' => $this->module->l('Facebook Business Manager', 'PsFacebookTranslations'),
                        'facebookPixel' => $this->module->l('Facebook Pixel', 'PsFacebookTranslations'),
                        'facebookPixelTooltip' => $this->module->l('Facebook Pixel', 'PsFacebookTranslations'),
                        'facebookPage' => $this->module->l('Facebook Page', 'PsFacebookTranslations'),
                        'facebookPageTooltip' => $this->module->l('Facebook Page', 'PsFacebookTranslations'),
                        'facebookAds' => $this->module->l('Facebook Ads', 'PsFacebookTranslations'),
                        'facebookAdsTooltip' => $this->module->l('Facebook Ads', 'PsFacebookTranslations'),
                        'manageFbeButton' => $this->module->l('Open Advanced settings', 'PsFacebookTranslations'),
                    ],
                ],
                'app' => [
                    'like' => $this->module->l('like', 'PsFacebookTranslations'),
                    'likes' => $this->module->l('likes', 'PsFacebookTranslations'),
                    'createdAt' => $this->module->l('Created', 'PsFacebookTranslations'),
                    'lastActive' => $this->module->l('Last active', 'PsFacebookTranslations'),
                    'activated' => $this->module->l('Activated', 'PsFacebookTranslations'),
                    'disabled' => $this->module->l('Disabled', 'PsFacebookTranslations'),
                    'viewStats' => $this->module->l('View stats', 'PsFacebookTranslations'),
                ],
                'glass' => [
                    'text' => $this->module->l('You don’t see Facebook secured browser? We help you relaunch the window to finish configuration. You may need to activate popup windows in your browser to continue.', 'PsFacebookTranslations'),
                    'link' => $this->module->l('Continue', 'PsFacebookTranslations'),
                ],
            ],

            'catalogSummary' => [
                'categoryMatching' => $this->module->l('Category matching', 'PsFacebookTranslations'),
                'categoryMatchingIntro' => $this->module->l('Match your own categories with official Facebook categories. \nIt helps get better ad performance and activate shop plugins.', 'PsFacebookTranslations'),
                'matchCategoriesButton' => $this->module->l('Match categories', 'PsFacebookTranslations'),
                'productCatalogExport' => $this->module->l('Product catalog export', 'PsFacebookTranslations'),
                'exportCatalogButton' => $this->module->l('Export catalog', 'PsFacebookTranslations'),
                'catalogExportWarning' => $this->module->l('Only products from matched categories are synced.', 'PsFacebookTranslations'),
                'catalogExportIntro' => $this->module->l('To sell and advertise your products on Facebook and Instagram, you need to export your product catalog.', 'PsFacebookTranslations'),
                'reportingTitle' => $this->module->l('Product sync report', 'PsFacebookTranslations'),
                'reportingIntro' => $this->module->l('Only products from matched categories are synced.', 'PsFacebookTranslations'),
                'viewButton' => $this->module->l('View', 'PsFacebookTranslations'),
                'backButton' => $this->module->l('Back', 'PsFacebookTranslations'),
                'detailsButton' => $this->module->l('See details', 'PsFacebookTranslations'),
            ],
            'categoryMatching' => [
                'intro' => $this->module->l('Match your own categories with official Facebook categories to improve catalog quality and ad performance.', 'PsFacebookTranslations'),
                'counterSubTitle' => $this->module->l('specified parent categories', 'PsFacebookTranslations'),
                'autocomplete' => [
                    'typeToFilter' => $this->module->l('Type to filter', 'PsFacebookTranslations'),
                    'select' => $this->module->l('Select', 'PsFacebookTranslations'),
                    'noResult' => $this->module->l('No result for your search', 'PsFacebookTranslations'),
                    'tooManyResults' => $this->module->l('Too many results, please complete your search.', 'PsFacebookTranslations'),
                    'fetchError' => $this->module->l('An error occurred during search process.', 'PsFacebookTranslations'),
                ],
            ],
            'integrate' => [
                'buttons' => [
                    'add' => $this->module->l('Add', 'PsFacebookTranslations'),
                    'syncProducts' => $this->module->l('Sync products', 'PsFacebookTranslations'),
                    'modalConfirm' => $this->module->l('Yes, confirm', 'PsFacebookTranslations'),
                ],
                'features' => [
                    'ig_shopping' => [
                        'name' => $this->module->l('Instagram shopping', 'PsFacebookTranslations'),
                        'description' => $this->module->l('Tag your products in the publications to redirect traffic to your e-commerce site.', 'PsFacebookTranslations'),
                    ],
                    'messenger_chat' => [
                        'name' => $this->module->l('Messenger Chat Plugin', 'PsFacebookTranslations'),
                        'description' => $this->module->l('Allows people to start a conversation with you on your website and continue in Messenger.', 'PsFacebookTranslations'),
                    ],
                    'page_shop' => [
                        'name' => $this->module->l('Facebook Shop', 'PsFacebookTranslations'),
                        'description' => $this->module->l("Bring your company's most important goal to the fore on your facebook page.", 'PsFacebookTranslations'),
                    ],
                    'page_cta' => [
                        'name' => $this->module->l('Call-to-action', 'PsFacebookTranslations'),
                        'description' => $this->module->l("Bring your company's most important goal to the fore on your facebook page.", 'PsFacebookTranslations'),
                    ],
                ],
                'headings' => [
                    'disabledFeatures' => $this->module->l('To boost sales, add to your shop...', 'PsFacebookTranslations'),
                    'unavailableFeatures' => $this->module->l('Unavailable features at the moment', 'PsFacebookTranslations'),
                ],
                'warning' => [
                    'productsNotSynced' => $this->module->l('You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.', 'PsFacebookTranslations'),
                    'disableFeatureModalHeader' => $this->module->l('Confirm deactivation?', 'PsFacebookTranslations'),
                    'disableFeatureModalText' => $this->module->l('You are about to disable this feature. This may limit some functionality.', 'PsFacebookTranslations'),
                ],
                'error' => [
                    'failedToUpdateFeature' => $this->module->l('Failed to update facebook feature.'),
                ],
            ],
        ];

        return $translations;
    }
}
