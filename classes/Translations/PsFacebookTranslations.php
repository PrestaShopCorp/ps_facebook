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
                    'catalog' => $this->module->l('Product catalog', 'PsFacebookTranslations'),
                    'help' => $this->module->l('Help', 'PsFacebookTranslations'),
                    'integrate' => $this->module->l('Sales channels', 'PsFacebookTranslations'),
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
                    'proPoint2Title' => $this->module->l('Understand your traffic', 'PsFacebookTranslations'),
                    'proPoint2Description' => $this->module->l('Use data from Facebook Pixel to understand actions people take on your website.', 'PsFacebookTranslations'),
                    'proPoint3Title' => $this->module->l('Boost your sales', 'PsFacebookTranslations'),
                    'proPoint3Description' => $this->module->l("Manage your product catalog and \nunlock the power of ads.", 'PsFacebookTranslations'),
                    'proPoint4Title' => $this->module->l('Create and optimize better shopping experience', 'PsFacebookTranslations'),
                    'proPoint4Description' => $this->module->l('Thanks to the automatic synchronisation of the product catalogue and the mapping of your categories have an updated and more efficient Facebook catalogue.', 'PsFacebookTranslations'),
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
                    'title' => $this->module->l('Your Facebook settings', 'PsFacebookTranslations'),
                    'notConnected' => [
                        'intro' => $this->module->l('Integrate your shop with Facebook.', 'PsFacebookTranslations'),
                        'connectButton' => $this->module->l('Connect to Facebook', 'PsFacebookTranslations'),
                        'description' => $this->module->l('With PS Facebook enjoy a frictionless, codeless onboarding and management experience for Facebook business products.', 'PsFacebookTranslations'),
                        'details' => $this->module->l("- Create/Select business \n- Create/Select page \n- Create/Select ad account \n- Create/Select install pixel \n - Create/Select sync product catalog \n - Create Facebook Shop & Instagram Shopping \n- Customize your Facebook Page with call-to-action", 'PsFacebookTranslations'),
                    ],
                    'connected' => [
                        'description' => $this->module->l('You authorize this Facebook account to connect to your store:', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Restart onboarding', 'PsFacebookTranslations'),
                        'unlinkButton' => $this->module->l('Unlink', 'PsFacebookTranslations'),
                        'facebookBusinessManager' => $this->module->l('Facebook Business Manager', 'PsFacebookTranslations'),
                        'facebookBusinessManagerTooltip' => $this->module->l('Facebook business account allows you to manage advertising accounts, Pages and the people who work on them in the same place', 'PsFacebookTranslations'),
                        'facebookPixel' => $this->module->l('Facebook Pixel', 'PsFacebookTranslations'),
                        'facebookPixelTooltip' => $this->module->l('The Facebook pixel is a piece of code (Javascript) that we automatically install on your website pages. It allows you to “track” who visits your website, track conversions of your Facebook ads and create retargeting audiences', 'PsFacebookTranslations'),
                        'facebookPage' => $this->module->l('Facebook Page', 'PsFacebookTranslations'),
                        'facebookPageTooltip' => $this->module->l('Your Facebook page will represent your business in your ads', 'PsFacebookTranslations'),
                        'facebookAds' => $this->module->l('Facebook Ads', 'PsFacebookTranslations'),
                        'facebookAdsTooltip' => $this->module->l('Facebook Ads account allows merchants to access their ads management tool, to make, edit and analyze paid promotional Facebook campaigns', 'PsFacebookTranslations'),
                        'manageFbeButton' => $this->module->l('Open advanced settings', 'PsFacebookTranslations'),
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
                    'informationCannotBeDisplayedWarning' => $this->module->l('This information cannot be displayed at the moment', 'PsFacebookTranslations'),
                ],
                'glass' => [
                    'text' => $this->module->l('You don’t see Facebook secured browser? We help you relaunch the window to finish configuration. You may need to activate popup windows in your browser to continue.', 'PsFacebookTranslations'),
                    'link' => $this->module->l('Continue', 'PsFacebookTranslations'),
                ],
            ],

            'catalogSummary' => [
                'categoryMatching' => $this->module->l('Enhance your catalog', 'PsFacebookTranslations'),
                'categoryMatchingIntro' => $this->module->l('To enhance your catalog and help customers discover your items online, enter a Facebook product category (FPC) for your items and then add more information specific to each category. We recommend that you choose the most specific category possible that applies to each item to help customers understand what type of item you’re selling.', 'PsFacebookTranslations'),
                'categoryMatchingNotice' => $this->module->l('You [1]must[/1] choose at least one of these category types: 1) to use onsite Facebook checkout and 2) to enhance your catalog with category-specific attributes. Both category types are optional, but conditionally required.', 'PsFacebookTranslations'),
                'matchCategoriesButton' => $this->module->l('Map categories', 'PsFacebookTranslations'),

                'productCatalogExport' => $this->module->l('Your catalog in Facebook Business', 'PsFacebookTranslations'),
                'exportCatalogButton' => $this->module->l('Share catalog', 'PsFacebookTranslations'),
                'catalogExportIntro' => $this->module->l("Before buyers can purchase items from you, you'll need to upload your products information into a Facebook Product Catalog. A catalog is a container that holds information about the items you want to advertise or sell across Facebook and Instagram.", 'PsFacebookTranslations'),
                'catalogExportWarning' => $this->module->l('By sharing your catalogue, you agree PrestaShop may send all information related to the catalog products to Facebook.', 'PsFacebookTranslations'),
                'reportingTitle' => $this->module->l('Product sync report', 'PsFacebookTranslations'),
                'reportingIntro' => $this->module->l('It can take up to 24h for your PrestaShop product information to sync to Facebook. Once products are published you can start tagging them on Instagram or create Facebook ads.', 'PsFacebookTranslations'),
                'pauseButton' => $this->module->l('Pause', 'PsFacebookTranslations'),
                'restartButton' => $this->module->l('Restart', 'PsFacebookTranslations'),
                'viewButton' => $this->module->l('View', 'PsFacebookTranslations'),
                'backButton' => $this->module->l('Back', 'PsFacebookTranslations'),
                'detailsButton' => $this->module->l('See details', 'PsFacebookTranslations'),
                'exportCatalogStatusPaused' => $this->module->l('The operation is paused', 'PsFacebookTranslations'),
                'exportCatalogStatusInProgress' => $this->module->l('In progress', 'PsFacebookTranslations'),
                'exportCatalogStatusDone' => $this->module->l('Your product catalog is now synced!', 'PsFacebookTranslations'),
                'exportCatalogButtonErrored' => $this->module->l('Failed! Try again', 'PsFacebookTranslations'),
            ],
            'categoryMatching' => [
                'title' => $this->module->l('Enhance your catalog : category mapping', 'PsFacebookTranslations'),
                'intro' => $this->module->l('To enhance your catalog and help customers discover your items online, enter a Facebook product category (FPC) for your items and then add more information specific to each category. We recommend that you choose the most specific category possible that applies to each item to help customers understand what type of item you’re selling.', 'PsFacebookTranslations'),
                'counterSubTitle' => $this->module->l('specified parent categories', 'PsFacebookTranslations'),
                'autocomplete' => [
                    'typeToFilter' => $this->module->l('Type to filter', 'PsFacebookTranslations'),
                    'select' => $this->module->l('Select', 'PsFacebookTranslations'),
                    'noResult' => $this->module->l('No result for your search', 'PsFacebookTranslations'),
                    'tooManyResults' => $this->module->l('Too many results, please complete your search.', 'PsFacebookTranslations'),
                    'fetchError' => $this->module->l('An error occurred during search process.', 'PsFacebookTranslations'),
                ],
                'tableMatching' => [
                    'firstTd' => $this->module->l('Category on your site'),
                    'secondTd' => $this->module->l('Facebook category'),
                    'thirdTd' => $this->module->l('Parent category'),
                    'fourthTd' => $this->module->l('Facebook subcategory'),
                ],
            ],
            'integrate' => [
                'buttons' => [
                    'syncProducts' => $this->module->l('Sync products', 'PsFacebookTranslations'),
                    'modalConfirm' => $this->module->l('Yes, confirm', 'PsFacebookTranslations'),
                ],
                'features' => [
                    'ig_shopping' => [
                        'name' => $this->module->l('Instagram shopping', 'PsFacebookTranslations'),
                        'description' => $this->module->l('Instagram Shopping gives your business an immersive storefront for people to explore your best products.', 'PsFacebookTranslations'),
                        'addButton' => $this->module->l('Add', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Manage', 'PsFacebookTranslations'),
                    ],
                    'messenger_chat' => [
                        'name' => $this->module->l('Messenger Chat Plugin', 'PsFacebookTranslations'),
                        'description' => $this->module->l('The Chat Plugin allows you to integrate your Messenger experience directly into your website.', 'PsFacebookTranslations'),
                        'addButton' => $this->module->l('Add', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Customize settings', 'PsFacebookTranslations'),
                    ],
                    'page_shop' => [
                        'name' => $this->module->l('Facebook Page Shops', 'PsFacebookTranslations'),
                        'description' => $this->module->l("Facebook Page shop allows you to list products you're selling and connect with more customers on Facebook.", 'PsFacebookTranslations'),
                        'addButton' => $this->module->l('Add', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Manage', 'PsFacebookTranslations'),
                    ],
                    'page_cta' => [
                        'name' => $this->module->l('Call-to-action', 'PsFacebookTranslations'),
                        'description' => $this->module->l('Add a button on your Facebook Page to get people to take an action from your page such as your shop.', 'PsFacebookTranslations'),
                        'addButton' => $this->module->l('Add', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Edit', 'PsFacebookTranslations'),
                    ],
                ],
                'headings' => [
                    'availableFeatures' => $this->module->l('To boost sales, add to your shop...', 'PsFacebookTranslations'),
                    'unavailableFeatures' => $this->module->l('Unavailable features at the moment', 'PsFacebookTranslations'),
                ],
                'warning' => [
                    'productsNotSynced' => $this->module->l('You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.', 'PsFacebookTranslations'),
                    'disableFeatureModalHeader' => $this->module->l('Confirm deactivation?', 'PsFacebookTranslations'),
                    'disableFeatureModalText' => $this->module->l('You are about to disable this feature. This may limit some functionality.', 'PsFacebookTranslations'),
                ],
                'error' => [
                    'failedToUpdateFeature' => $this->module->l('Failed to update facebook feature.', 'PsFacebookTranslations'),
                ],
            ],

            'help' => [
                'title' => $this->module->l('Help for PrestaShop Facebook', 'PsFacebookTranslations'),
                'allowsYouTo' => [
                    'title' => $this->module->l('This module allows you to:', 'PsFacebookTranslations'),
                    'business' => $this->module->l('Manage your business', 'PsFacebookTranslations'),
                    'account' => $this->module->l('Manage your ad account', 'PsFacebookTranslations'),
                    'traffic' => $this->module->l('Understand your traffic', 'PsFacebookTranslations'),
                    'inventory' => $this->module->l('Build and manage inventory', 'PsFacebookTranslations'),
                    'people' => $this->module->l('Reach more people', 'PsFacebookTranslations'),
                ],
                'help' => [
                    'needHelp' => $this->module->l('Need help? Find here the documentation of this module.', 'PsFacebookTranslations'),
                    'downloadPdf' => $this->module->l('Download PDF', 'PsFacebookTranslations'),
                    'couldntFindAnyAnswer' => $this->module->l('Couldn\'t find any answer to your question?', 'PsFacebookTranslations'),
                    'contactUs' => $this->module->l('Contact us', 'PsFacebookTranslations'),
                ],
            ],
            'faq' => [
                'title' => $this->module->l('FAQ', 'PsFacebookTranslations'),
                'noFaq' => $this->module->l('No FAQ available.', 'PsFacebookTranslations'),
            ],
        ];

        return $translations;
    }
}
