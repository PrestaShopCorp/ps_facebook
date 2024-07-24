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

namespace PrestaShop\Module\PrestashopFacebook\Config;

class Config
{
    public const API_VERSION = 'v17.0';

    public const COMPLIANT_PS_ACCOUNTS_VERSION = '3.0.0';
    public const REQUIRED_PS_ACCOUNTS_VERSION = '4.0.0';
    public const REQUIRED_PS_CLOUDSYNC_VERSION = '1.9.4';

    public const USE_LOCAL_VUE_APP = false;
    public const USE_LIVE_VUE_APP = false;
    public const PSX_FACEBOOK_CDN_URL = 'https://storage.googleapis.com/psxfacebook/v1.x.x/js/';

    public const HOOK_LIST = [
        'displayHeader',
        'actionCustomerAccountAdd',
        'actionCartSave',
        'actionSearch',
        'displayOrderConfirmation',
        'actionAjaxDieProductControllerDisplayAjaxQuickviewAfter',
        'actionObjectCustomerMessageAddAfter',
        'displayFooter',
        'actionNewsletterRegistrationAfter',
        'displayBackOfficeHeader',
        'actionFrontControllerSetMedia',
        'actionFacebookCallPixel',
    ];

    public const PS_PIXEL_ID = 'PS_FACEBOOK_PIXEL_ID';
    public const PS_FACEBOOK_USER_ACCESS_TOKEN = 'PS_FACEBOOK_ACCESS_TOKEN';
    public const PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE = 'PS_FACEBOOK_USER_ACCESS_TOKEN_EXPIRATION_DATE';
    public const PS_FACEBOOK_SYSTEM_ACCESS_TOKEN = 'PS_FACEBOOK_SYSTEM_ACCESS_TOKEN';
    public const PS_FACEBOOK_PROFILES = 'PS_FACEBOOK_PROFILES';
    public const PS_FACEBOOK_PAGES = 'PS_FACEBOOK_PAGES';
    public const PS_FACEBOOK_BUSINESS_MANAGER_ID = 'PS_FACEBOOK_BUSINESS_MANAGER_ID';
    public const PS_FACEBOOK_AD_ACCOUNT_ID = 'PS_FACEBOOK_AD_ACCOUNT_ID';
    public const PS_FACEBOOK_CATALOG_ID = 'PS_FACEBOOK_CATALOG_ID';
    public const PS_FACEBOOK_EXTERNAL_BUSINESS_ID = 'PS_FACEBOOK_EXTERNAL_BUSINESS_ID';
    public const PS_FACEBOOK_PIXEL_ENABLED = 'PS_FACEBOOK_PIXEL_ENABLED';
    public const PS_FACEBOOK_CAPI_TEST_EVENT_CODE = 'PS_FACEBOOK_CAPI_TEST_EVENT_CODE';
    public const PS_FACEBOOK_PRODUCT_SYNC_FIRST_START = 'PS_FACEBOOK_PRODUCT_SYNC_FIRST_START';
    public const PS_FACEBOOK_PRODUCT_SYNC_ON = 'PS_FACEBOOK_PRODUCT_SYNC_ON';

    public const AVAILABLE_FBE_FEATURES = ['page_cta', 'page_shop'/*, 'ig_shopping'*/];
    public const CONFIGURABLE_FBE_FEATURES = [];
    public const FBE_FEATURES_REQUIRING_PRODUCT_SYNC = ['page_shop', 'ig_shopping'];
    public const FBE_FEATURE_CONFIGURATION = 'PS_FACEBOOK_FBE_FEATURE_CONFIG_';

    public const CATEGORIES_PER_PAGE = 50;
    public const MAX_CATEGORY_DEPTH = 3;

    public const REPORTS_PER_PAGE = 1000;

    // Data that can be overwritten by .env file if using the Env class
    public const PSX_FACEBOOK_API_URL = 'https://facebook-api.psessentials.net';
    public const PSX_FACEBOOK_UI_URL = 'https://facebook.psessentials.net';
    public const PSX_FACEBOOK_APP_ID = '726899634800479';
    public const PSX_FACEBOOK_SENTRY_CREDENTIALS = 'https://c5dacaa8aca74c458179b113b646774c@o298402.ingest.sentry.io/5531852';
    public const PSX_FACEBOOK_SEGMENT_API_KEY = 'vgBkyeNDK7tQwgxrxoVUGRMNGTUATiPw';

    /** @see https://developers.facebook.com/docs/marketing-api/error-reference */
    public const OAUTH_EXCEPTION_CODE = [33, 190];
    public const PS_FACEBOOK_CAPI_PARTNER_AGENT = 'prestashop';

    public const PS_FACEBOOK_FORCED_DISCONNECT = 'PS_FACEBOOK_FORCED_DISCONNECT';
    public const PS_FACEBOOK_SUSPENSION_REASON = 'PS_FACEBOOK_SUSPENSION_REASON';
}
