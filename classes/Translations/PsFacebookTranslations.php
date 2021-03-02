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
                    'subTitle' => $this->module->l('Easily and quickly build your business presence into Facebook community!', 'PsFacebookTranslations'),
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
                    'stepperTitle' => $this->module->l('Step-by-step guide', 'PsFacebookTranslations'),
                    'stepPsAccount' => $this->module->l('Connect your PrestaShop account', 'PsFacebookTranslations'),
                    'stepPsFacebook' => $this->module->l('Connect your store to Facebook', 'PsFacebookTranslations'),
                    'stepCategoryMatching' => $this->module->l('Map categories', 'PsFacebookTranslations'),
                    'stepCategoryMatchingOptional' => $this->module->l('(optional but strongly recommended)', 'PsFacebookTranslations'),
                    'stepProductSync' => $this->module->l('Export your product catalog', 'PsFacebookTranslations'),
                    'stepAdCampaign' => $this->module->l('Create Traffic and/or Dynamic Ads', 'PsFacebookTranslations'),
                    'reloadButton' => $this->module->l('Reload', 'PsFacebookTranslations'),
                    'shopInConflictError' => $this->module->l('You are already connected to Facebook from another shop. At the moment, PrestaShop Facebook can only be configured on one shop. You must log out of your first shop to connect this one.', 'PsFacebookTranslations'),
                    'unknownOnboardingError' => $this->module->l('An unknown error occurred during onboarding process. Please reload and try again.', 'PsFacebookTranslations'),
                    'psAccountUpgradeNeededWarning' => $this->module->l("The version of the module PrestaShop Account running on this shop (v{psAccountsVersion}) is older than the minimum required v{requiredPsAccountsVersion}.\n\nYou may use PrestaShop Facebook but some features (i.e product synchronization) won't be available until you upgrade PrestaShop Account.", 'PsFacebookTranslations'),
                    'psAccountUpgradeButton' => $this->module->l('Upgrade PrestaShop Accounts', 'PsFacebookTranslations'),
                    'psAccountUpgradeDone' => $this->module->l('PrestaShop Account has been successfully upgraded.', 'PsFacebookTranslations'),
                ],
                'facebook' => [
                    'title' => $this->module->l('Your Facebook settings', 'PsFacebookTranslations'),
                    'notConnected' => [
                        'title' => $this->module->l('Connect your store to Facebook', 'PsFacebookTranslations'),
                        'intro' => $this->module->l('Integrate your shop with Facebook.', 'PsFacebookTranslations'),
                        'connectButton' => $this->module->l('Connect to Facebook', 'PsFacebookTranslations'),
                        'description' => $this->module->l('With PS Facebook enjoy a frictionless, codeless onboarding and management experience for Facebook business products.', 'PsFacebookTranslations'),
                        'details' => $this->module->l("- Create/Select business \n- Create/Select page \n- Create/Select ad account \n- Create/Select install pixel \n - Create/Select sync product catalog \n - Create Facebook Shop & Instagram Shopping \n- Add Messenger plug-in in your shop", 'PsFacebookTranslations'),
                    ],
                    'connected' => [
                        'description' => $this->module->l('You authorize this Facebook account to connect to your store:', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Modify the account settings', 'PsFacebookTranslations'),
                        'unlinkButton' => $this->module->l('Logout', 'PsFacebookTranslations'),
                        'facebookBusinessManager' => $this->module->l('Facebook Business Manager', 'PsFacebookTranslations'),
                        'facebookBusinessManagerTooltip' => $this->module->l('Facebook business account allows you to manage advertising accounts, Pages and the people who work on them in the same place', 'PsFacebookTranslations'),
                        'facebookPixel' => $this->module->l('Facebook Pixel', 'PsFacebookTranslations'),
                        'facebookPixelTooltip' => $this->module->l('The Facebook pixel is a piece of code (Javascript) that we automatically install on your website pages. It allows you to “track” who visits your website, track conversions of your Facebook ads and create retargeting audiences', 'PsFacebookTranslations'),
                        'facebookPage' => $this->module->l('Facebook Page', 'PsFacebookTranslations'),
                        'facebookPageTooltip' => $this->module->l('Your Facebook page will represent your business in your ads', 'PsFacebookTranslations'),
                        'facebookAds' => $this->module->l('Facebook Ads', 'PsFacebookTranslations'),
                        'facebookAdsTooltip' => $this->module->l('Facebook Ads account allows merchants to access their ads management tool, to make, edit and analyze paid promotional Facebook campaigns', 'PsFacebookTranslations'),
                        'manageFbeButton' => $this->module->l('Go to Facebook Business', 'PsFacebookTranslations'),
                        'unlinkModalHeader' => $this->module->l('Confirm uninstallation?', 'PsFacebookTranslations'),
                        'unlinkModalText' => $this->module->l('You are about to remove Facebook Business Extension. You will no longer have access to the stored settings of Facebook.', 'PsFacebookTranslations'),
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
                'categoryMatchingIntro' => $this->module->l('To enhance your catalog and help customers discover your items online, enter a Google product category (GPC) for your items.', 'PsFacebookTranslations'),
                'categoryMatchingNotice' => $this->module->l('You **must** choose at least one of these category types: 1) to use onsite Facebook checkout and 2) to enhance your catalog with category-specific attributes. Both category types are optional, but conditionally required.', 'PsFacebookTranslations'),
                'matchCategoriesButton' => $this->module->l('Map categories', 'PsFacebookTranslations'),

                'productCatalogExport' => $this->module->l('Your catalog in Facebook Business', 'PsFacebookTranslations'),
                'exportCatalogButton' => $this->module->l('Share catalog', 'PsFacebookTranslations'),
                'catalogExportIntro' => $this->module->l('Before buyers can purchase items from you, you\'ll need to upload your products information into a Facebook Product Catalog.', 'PsFacebookTranslations'),
                'catalogExportInfo' => $this->module->l("Please be aware of the following: \n1. The export of the catalog is done once a day (during the night). \n2. It can take up to 24h for your PrestaShop product information to sync to Facebook. \n3. Only all « active » products are exported to Facebook for their base language. \n4. Only products with mandatory fields filled will be synchronized: a (short) description, a quantity, a price, a cover, a manufacturer or an ean/upc/isbn. \n5. Export of product localizations (for translations) are still in beta and could trigger some unexpected errors. \n6. Only products that have been modified will be updated in your catalog.", 'PsFacebookTranslations'),
                'resetExportLink' => $this->module->l('If you want to re-export your entire catalog click here.', 'PsFacebookTranslations'),
                'resetExportSuccess' => $this->module->l('Next export will contain all your catalog.', 'PsFacebookTranslations'),
                'resetExportError' => $this->module->l('Unknown error occurred trying to reset export. Please try again later.', 'PsFacebookTranslations'),
                'preApprovalScanTitle' => $this->module->l('Product scan before synchronization to facebook', 'PsFacebookTranslations'),
                'preApprovalScanIntro' => $this->module->l('Check which products are ready to be synchronized and which ones have problems and will not be synchronized.', 'PsFacebookTranslations'),
                'preApprovalScanRefreshDate' => $this->module->l('Last scan today at {0}', 'PsFacebookTranslations'),
                'preApprovalScanReadyToSync' => $this->module->l('Ready to sync', 'PsFacebookTranslations'),
                'preApprovalScanNonSyncable' => $this->module->l('Non-syncable', 'PsFacebookTranslations'),
                'catalogExportWarning' => $this->module->l('Products will be exported then updated every 24h.', 'PsFacebookTranslations'),
                'catalogExportDisclaimer' => $this->module->l('By sharing your catalog, you agree PrestaShop may send all information related to the catalog products to Facebook.', 'PsFacebookTranslations'),
                'catalogExportPaused' => $this->module->l('Paused', 'PsFacebookTranslations'),
                'catalogExportActivated' => $this->module->l('Activated', 'PsFacebookTranslations'),
                'catalogExportNotice' => $this->module->l('It can take up to 24h for your PrestaShop product information to sync to Facebook.', 'PsFacebookTranslations'),
                'catalogExportOperationPaused' => $this->module->l('The operation is paused.', 'PsFacebookTranslations'),
                'viewButton' => $this->module->l('View', 'PsFacebookTranslations'),
                'viewCatalogButton' => $this->module->l('View catalog', 'PsFacebookTranslations'),
                'backButton' => $this->module->l('Back', 'PsFacebookTranslations'),
                'detailsButton' => $this->module->l('See details', 'PsFacebookTranslations'),
                'showMore' => $this->module->l('Show more', 'PsFacebookTranslations'),
                'showLess' => $this->module->l('Show less', 'PsFacebookTranslations'),
                'exportCatalogButtonErrored' => $this->module->l('Failed! Try again', 'PsFacebookTranslations'),
                'modalDeactivationTitle' => $this->module->l('Confirm deactivation?', 'PsFacebookTranslations'),
                'modalDeactivationText' => $this->module->l('You are about to disable your catalog sync with Facebook Business. Products will not sync until catalog export is reactivated.', 'PsFacebookTranslations'),
                'reportingLastSync' => $this->module->l('Last sync', 'PsFacebookTranslations'),
                'reportingCatalogCount' => $this->module->l('Products in Facebook catalog', 'PsFacebookTranslations'),
                'reportingErrorsCount' => $this->module->l('Errors due to catalog export', 'PsFacebookTranslations'),
            ],
            'syncReport' => [
                'title' => $this->module->l('Product status', 'PsFacebookTranslations'),
                'views' => [
                    'prevalidation' => $this->module->l('Before export{0}', 'PsFacebookTranslations'),
                    'reporting' => $this->module->l('After export{0}', 'PsFacebookTranslations'),
                    'oneError' => $this->module->l(' (1 error)', 'PsFacebookTranslations'),
                    'manyErrors' => $this->module->l(' ({0} errors)', 'PsFacebookTranslations'),
                ],
                'id' => $this->module->l('ID', 'PsFacebookTranslations'),
                'name' => $this->module->l('Name', 'PsFacebookTranslations'),
                'language' => $this->module->l('Language', 'PsFacebookTranslations'),
                'languageTooltip' => $this->module->l('This is the language in which the error is found.', 'PsFacebookTranslations'),
                'image' => $this->module->l('Image', 'PsFacebookTranslations'),
                'imageTooltip' => $this->module->l('The main image of your item. Images must be in JPG, GIF or PNG format, at least 500 x 500 pixels and up to 8 MB. See product image specifications.', 'PsFacebookTranslations'),
                'description' => $this->module->l('Description', 'PsFacebookTranslations'),
                'descriptionTooltip' => $this->module->l('A short, relevant description of the item. Include specific and unique product features like material or color. Use plain text (not HTML) and don\'t enter text in all capital letters or include any links. See product description specifications. Character limit: 5,000.', 'PsFacebookTranslations'),
                'barcode' => $this->module->l('Barcode/Brand', 'PsFacebookTranslations'),
                'barcodeTooltip' => $this->module->l('The brand name, unique manufacturer part number (MPN) or Global Trade Item Number (GTIN) of the item. You only need to enter one of these, not all of them. For GTIN, enter the item\'s UPC, EAN, JAN or ISBN. Character limit: 100.', 'PsFacebookTranslations'),
                'price' => $this->module->l('Price', 'PsFacebookTranslations'),
                'action' => $this->module->l('Action', 'PsFacebookTranslations'),
                'error' => $this->module->l('Error type', 'PsFacebookTranslations'),
                'errorTooltip' => $this->module->l('This is the error given by Facebook when trying to export the product.', 'PsFacebookTranslations'),
                'prevalidationText' => $this->module->l('Products with detected problems and which couldn\'t be synced are listed below:', 'PsFacebookTranslations'),
                'reportingText' => $this->module->l('Products with detected problems after catalog export are listed below:', 'PsFacebookTranslations'),
                'lastSyncDate' => $this->module->l('Last sync {0} at {1}', 'PsFacebookTranslations'),
                'otherLanguage' => $this->module->l('Other', 'PsFacebookTranslations'),
                'prevalidationEmpty' => $this->module->l('Well done! All your products are ready to be exported', 'PsFacebookTranslations'),
                'reportingEmpty' => $this->module->l('Well done! All your products have been exported to your Facebook catalog.', 'PsFacebookTranslations'),
            ],
            'categoryMatching' => [
                'title' => $this->module->l('Enhance your catalog : category mapping', 'PsFacebookTranslations'),
                'intro' => $this->module->l('To enhance your catalog and help customers discover your items online, enter a Google product category (GPC) for your items.', 'PsFacebookTranslations'),
                'counterSubTitle' => $this->module->l('specified categories', 'PsFacebookTranslations'),
                'edit' => $this->module->l('Edit', 'PsFacebookTranslations'),
                'autocomplete' => [
                    'typeToFilter' => $this->module->l('Type to filter', 'PsFacebookTranslations'),
                    'select' => $this->module->l('Select', 'PsFacebookTranslations'),
                    'noResult' => $this->module->l('No result for your search', 'PsFacebookTranslations'),
                    'tooManyResults' => $this->module->l('Too many results, please complete your search.', 'PsFacebookTranslations'),
                    'fetchError' => $this->module->l('An error occurred during search process.', 'PsFacebookTranslations'),
                ],
                'tableMatching' => [
                    'firstTd' => $this->module->l('Category on your site'),
                    'secondTd' => $this->module->l('Google category'),
                    'thirdTd' => $this->module->l('Apply to sub-categories?'),
                    'fourthTd' => $this->module->l('Google subcategory'),
                ],
                'editTable' => [
                    'required' => $this->module->l('Required'),
                    'checkboxTxt' => $this->module->l('Display unspecified categories only'),
                    'psCategoryName' => $this->module->l('Category on your site'),
                    'fbCategoryName' => $this->module->l('Google category'),
                    'fbSubcategoryName' => $this->module->l('Google subcategory'),
                ],
            ],
            'categoryMatched' => [
                'title' => $this->module->l('Enhance your catalog: category mapping'),
                'description' => $this->module->l('To enhance your catalog and help customers discover your items online, enter a Google product category (GPC) for your items.'),
                'btn' => $this->module->l('View mapping'),
                'progressBarTotal' => $this->module->l('categories mapped'),
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
                        'checkMessages' => $this->module->l('Check my messages', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Customize settings', 'PsFacebookTranslations'),
                    ],
                    'page_shop' => [
                        'name' => $this->module->l('Add a shop tab on your Facebook page', 'PsFacebookTranslations'),
                        'description' => $this->module->l("Facebook Page shop allows you to list products you're selling and connect with more customers on Facebook.", 'PsFacebookTranslations'),
                        'addButton' => $this->module->l('Add', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Manage', 'PsFacebookTranslations'),
                    ],
                    'page_cta' => [
                        'name' => $this->module->l('Add a button on your Facebook page', 'PsFacebookTranslations'),
                        'description' => $this->module->l('Add a button on your Facebook Page to get people to take an action from your page such as your shop.', 'PsFacebookTranslations'),
                        'addButton' => $this->module->l('Add', 'PsFacebookTranslations'),
                        'editButton' => $this->module->l('Edit', 'PsFacebookTranslations'),
                    ],
                ],
                'headings' => [
                    'availableFeatures' => $this->module->l('To boost sales, add to your shop...', 'PsFacebookTranslations'),
                    'unavailableFeatures' => $this->module->l('Unavailable features at the moment', 'PsFacebookTranslations'),
                ],
                'success' => [
                    'featureEnabled' => $this->module->l('You added {0}!', 'PsFacebookTranslations'),
                    'shopLink' => $this->module->l('View your shop', 'PsFacebookTranslations'),
                ],
                'warning' => [
                    'productsNotSynced' => $this->module->l('You first need to import your product catalog so you will be able to set up Facebook Shop and Instagram Shopping and also create ad campaigns.', 'PsFacebookTranslations'),
                    'disableFeatureModalHeader' => $this->module->l('Confirm deactivation?', 'PsFacebookTranslations'),
                    'disableFeatureModalText' => $this->module->l('You are about to disable this feature. This may limit some functionality.', 'PsFacebookTranslations'),
                    'noFeatures' => $this->module->l('Features could not be retrieved from Facebook. This may be caused by the token expiration and can be fixed by restarting the onboarding.', 'PsFacebookTranslations'),
                ],
                'error' => [
                    'failedToUpdateFeature' => $this->module->l('Failed to update facebook feature.', 'PsFacebookTranslations'),
                ],
            ],
            'survey' => [
                'title' => $this->module->l('Give us your feedback!', 'PsFacebookTranslations'),
                'text' => $this->module->l('Only 3 minutes to give some feedback on the configuration and use of PrestaShop Facebook!', 'PsFacebookTranslations'),
                'button' => $this->module->l("Let's begin", 'PsFacebookTranslations'),
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
            'productStatuses' => [
                'Approved' => $this->module->l('Approved'),
                'Pending' => $this->module->l('Pending'),
                'Disapproved' => $this->module->l('Disapproved'),
            ],
        ];

        return $translations;
    }
}
