<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <spinner v-if="loading" />
  <div
    id="configuration"
    class="ps-facebook-configuration-tab"
    v-else
  >
    <introduction
      v-if="!psAccountsOnboarded && showIntroduction"
      @onHide="showIntroduction = false"
      class="m-3"
    />
    <MultiStoreSelector
      v-else-if="!contextPsAccounts.isShopContext && shops.length"
      :shops="shops"
      class="m-3"
      @shop-selected="onShopSelected($event)"
    />
    <template v-else>
      <messages
        :show-onboard-succeeded="psFacebookJustOnboarded"
        :show-sync-catalog-advice="psAccountsOnboarded && showSyncCatalogAdvice"
        :category-matching-started="categoryMatchingStarted"
        :product-sync-started="productSyncStarted"
        :ad-campaign-started="adCampaignStarted"
        :error="error"
        @onSyncCatalogClick="onSyncCatalogClick"
        @onCategoryMatchingClick="onCategoryMatchingClick"
        @onAdCampaignClick="onAdCampaignClick"
        class="m-3"
      />
      <PsAccountsUpdateNeeded
        v-if="needsPsAccountsUpgrade"
      />
      <b-alert
        v-if="psAccountShopInConflict"
        variant="danger"
        class="m-3"
        show
        v-html="md2html($t('configuration.messages.shopInConflictError'))"
      />
      <ps-accounts
        v-else
        :context="contextPsAccounts"
        class="m-3"
      />

      <facebook-not-connected
        v-if="!facebookConnected"
        @onFbeOnboardClick="onFbeOnboardClick"
        class="m-3"
        :active="psAccountsOnboarded"
        :can-connect="!!dynamicExternalBusinessId"
      />
      <facebook-connected
        v-else
        :ps-facebook-app-id="psFacebookAppId"
        :external-business-id="dynamicExternalBusinessId"
        :context-ps-facebook="dynamicContextPsFacebook"
        @onEditClick="onEditClick"
        @onPixelActivation="onPixelActivation"
        @onUninstallClick="onUninstallClick"
        class="m-3"
      />
      <survey v-if="facebookConnected" />
      <div
        v-if="showGlass"
        class="glass"
        @click="glassClicked"
      >
        <div class="refocus">
          <img
            class="m-3"
            src="@/assets/facebook_white_logo.svg"
            width="56"
            height="56"
            alt="PS Facebook logo"
          >
          <p>{{ $t('configuration.glass.text') }}</p>
          <a href="javascript:void(0)">{{ $t('configuration.glass.link') }}</a>
        </div>
        <div
          class="closeCross p-1 m-4"
          @click="closePopup"
        >
          <i class="material-icons">close</i>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {MultiStoreSelector, PsAccounts} from 'prestashop_accounts_vue_components';
import Showdown from 'showdown';
import Spinner from '../components/spinner/spinner.vue';
import Introduction from '../components/configuration/introduction.vue';
import Messages from '../components/configuration/messages.vue';
import NoConfig from '../components/configuration/no-config.vue';
import FacebookConnected from '../components/configuration/facebook-connected.vue';
import FacebookNotConnected from '../components/configuration/facebook-not-connected.vue';
import Survey from '../components/survey/survey.vue';
import openPopupGenerator from '../lib/fb-login';
import PsAccountsUpdateNeeded from '../components/warning/ps-accounts-update-needed.vue';

const generateOpenPopup = (component, popupUrl) => {
  const canGeneratePopup = (
    component.contextPsAccounts.currentShop
    && component.contextPsAccounts.currentShop.url
    && component.dynamicExternalBusinessId
    && component.psAccountsToken
  );
  return canGeneratePopup ? openPopupGenerator(
    window,
    component.contextPsAccounts.currentShop.url.replace(/^(https?:\/\/[^/]+)(.*)/, '$1'),
    popupUrl,
    '/index.html',
    component.contextPsAccounts.currentShop.name || 'Unnamed PrestaShop shop',
    component.dynamicExternalBusinessId,
    component.psAccountsToken,
    component.currency,
    component.timezone,
    component.locale,
    null,
    component.onFbeOnboardOpened,
    component.onFbeOnboardClosed,
    component.onFbeOnboardResponded,
  ) : () => { component.createExternalBusinessIdAndOpenPopup(); };
};

export default defineComponent({
  name: 'Configuration',
  components: {
    Spinner,
    Introduction,
    Messages,
    MultiStoreSelector,
    PsAccounts,
    PsAccountsUpdateNeeded,
    NoConfig,
    FacebookNotConnected,
    FacebookConnected,
    Survey,
  },
  mixins: [],
  props: {
    contextPsAccounts: {
      type: Object,
      required: false,
      default: () => global.contextPsAccounts,
    },
    contextPsFacebook: {
      type: Object,
      required: false,
      default: () => global.contextPsFacebook || {}, // fallback to {} is important!
    },
    psFacebookAppId: {
      type: String,
      required: false,
      default: () => global.psFacebookAppId,
    },
    externalBusinessId: {
      type: String,
      required: false,
      default: () => global.psFacebookExternalBusinessId,
    },
    psAccountsToken: {
      type: String,
      required: false,
      default: () => global.psAccountsToken,
    },
    psAccountShopInConflict: {
      type: Boolean,
      required: false,
      default: () => global.psAccountShopInConflict,
    },
    currency: {
      type: String,
      required: false,
      default: () => global.psFacebookCurrency || 'EUR',
    },
    timezone: {
      type: String,
      required: false,
      default: () => global.psFacebookTimezone || 'Europe/Paris',
    },
    locale: {
      type: String,
      required: false,
      default: () => global.psFacebookLocale || 'en-US',
    },
    pixelActivationRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookPixelActivationRoute || null,
    },
    fbeOnboardingSaveRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookFbeOnboardingSaveRoute || null,
    },
    fbeOnboardingUninstallRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookFbeOnboardingUninstallRoute || null,
    },
    psFacebookUiUrl: {
      type: String,
      required: false,
      default: () => global.psFacebookFbeUiUrl || null,
    },
    psFacebookRetrieveExternalBusinessId: {
      type: String,
      required: false,
      default: () => global.psFacebookRetrieveExternalBusinessId || null,
    },
    psAccountVersionCheck: {
      type: Boolean,
      required: false,
      default: () => global.psAccountVersionCheck,
    },
  },
  computed: {
    psAccountsOnboarded() {
      return this.contextPsAccounts.user
        && this.contextPsAccounts.user.email !== null
        && this.contextPsAccounts.user.emailIsValidated;
    },
    facebookConnected() {
      return (this.contextPsFacebook
        && this.contextPsFacebook.facebookBusinessManager
        && this.contextPsFacebook.facebookBusinessManager.id)
        || false;
    },
    categoryMatchingStarted() {
      return this.dynamicContextPsFacebook && this.dynamicContextPsFacebook.catalog
        && this.dynamicContextPsFacebook.catalog.categoryMatchingStarted;
    },
    productSyncStarted() {
      return this.contextPsFacebook && this.contextPsFacebook.catalog
        && this.contextPsFacebook.catalog.productSyncStarted;
    },
    adCampaignStarted() {
      // TODO !1: when true?
      return false;
    },
    showSyncCatalogAdvice() {
      const c = this.dynamicContextPsFacebook;
      return c && this.facebookConnected && (
        !c.catalog
        || (c.catalog.categoryMatchingStarted !== true || c.catalog.productSyncStarted !== true)
      );
    },
    needsPsAccountsUpgrade() {
      return this.psAccountVersionCheck && this.psAccountVersionCheck.needsPsAccountsUpgrade;
    },
  },
  data() {
    return {
      dynamicContextPsFacebook: this.contextPsFacebook,
      dynamicExternalBusinessId: this.externalBusinessId,
      showIntroduction: true, // Initialized to true except if a props should avoid the introduction
      psFacebookJustOnboarded: false, // Put this to true just after FBE onboarding is finished once
      openPopup: generateOpenPopup(this, this.psFacebookUiUrl),
      showGlass: false,
      error: null,
      loading: true,
      popupReceptionDuplicate: false,
      openedPopup: null,
      shops: this.contextPsAccounts.shops || [],
    };
  },
  created() {
    if (!this.contextPsFacebook || !this.externalBusinessId) {
      this.fetchData();
    } else {
      this.loading = false;
    }
  },
  methods: {
    fetchData() {
      this.loading = true;
      fetch(global.psFacebookGetFbContextRoute)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((json) => {
          this.$root.refreshContextPsFacebook(json.contextPsFacebook);
          this.dynamicExternalBusinessId = json.psFacebookExternalBusinessId;
          this.createExternalBusinessId();
          this.loading = false;
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
        });
    },
    onCategoryMatchingClick() {
      this.$router.push({name: 'Catalog', query: {page: 'categoryMatchingEdit'}});
    },
    onSyncCatalogClick() {
      this.$router.push({name: 'Catalog', query: {page: 'summary'}});
    },
    onAdCampaignClick() {
      const catalogId = this.dynamicContextPsFacebook.catalog.id;
      const businessId = this.dynamicContextPsFacebook.facebookBusinessManager.id;
      const host = 'https://business.facebook.com';
      const query = `?business_id=${businessId}&channel=COLLECTION_ADS`;
      const url = `${host}/products/catalogs/${catalogId}/ads${query}`;
      window.open(url, '_blank');
    },
    onFbeOnboardClick() {
      this.openedPopup = this.openPopup();
      this.showGlass = true;
    },
    onEditClick() {
      this.openedPopup = this.openPopup();
      this.showGlass = true;
    },
    onUninstallClick() {
      this.loading = true;
      fetch(this.fbeOnboardingUninstallRoute)
        .then((res) => {
          this.loading = false;
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((json) => {
          this.$root.refreshContextPsFacebook(json.contextPsFacebook);
          this.dynamicExternalBusinessId = json.psFacebookExternalBusinessId;
          this.createExternalBusinessId();
          this.facebookConnected = false;
          this.psFacebookJustOnboarded = false;
        }).catch((error) => {
          console.error(error);
          // TODO: Replace me with uninstallation specific message
          this.error = 'configuration.messages.unknownOnboardingError';
        });
    },
    onPixelActivation() {
      const actualState = this.dynamicContextPsFacebook.pixel.isActive;
      const newState = !actualState;
      // Save activation state in PHP side.
      fetch(this.pixelActivationRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({event_status: newState}),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (!res.success) {
          throw new Error(res.statusText || res.status);
        }
        this.$root.refreshContextPsFacebook({
          ...this.dynamicContextPsFacebook,
          pixel: {...this.dynamicContextPsFacebook.pixel, isActive: newState},
        });
      }).catch((error) => {
        console.error(error);
        this.error = 'configuration.messages.unknownOnboardingError';
        this.$root.refreshContextPsFacebook({
          ...this.dynamicContextPsFacebook,
          pixel: {...this.dynamicContextPsFacebook.pixel, isActive: actualState},
        });
      });
    },
    onFbeOnboardOpened() {
      this.showGlass = true;
    },
    onFbeOnboardClosed() {
      this.showGlass = false;
      this.openedPopup = null;
    },
    onFbeOnboardResponded(response) {
      if (this.popupReceptionDuplicate) {
        console.log('duplicated response received');
        return;
      }
      this.popupReceptionDuplicate = true;
      console.log('response received', response);

      if (!response.access_token) {
        this.showGlass = false;
        this.openedPopup = null;
        return;
      }
      this.loading = true;

      // Save access_token, fbe?, and more on PHP side. And gets back contextPsFacebook in response.
      fetch(this.fbeOnboardingSaveRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({onboarding: response}),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (!res.success) {
          throw new Error('Error!');
        }
        this.$segment.track('PS Account & FBE connected', {
          module: 'ps_facebook',
        });
        this.$root.refreshContextPsFacebook(res.contextPsFacebook);
        this.loading = false;
        this.showGlass = false;
        this.openedPopup = null;
        this.popupReceptionDuplicate = false;
        this.psFacebookJustOnboarded = true;
      }).catch((error) => {
        console.error(error);
        this.$segment.track('The pop-up gets blocked', {
          module: 'ps_facebook',
        });
        this.error = 'configuration.messages.unknownOnboardingError';
        this.loading = false;
        this.showGlass = false;
        this.openedPopup = null;
        this.popupReceptionDuplicate = false;
        this.$forceUpdate();
      });
    },
    onShopSelected(shopSelected) {
      window.location.href = shopSelected.url;
    },
    createExternalBusinessId() {
      if (!this.psFacebookRetrieveExternalBusinessId) {
        return Promise.reject(new Error('No route to fetch external Business Id'));
      }
      if (this.contextPsAccounts.currentShop
        && this.contextPsAccounts.currentShop.url
        && this.psAccountsToken) {
        if (this.dynamicExternalBusinessId) {
          this.openPopup = generateOpenPopup(this, this.psFacebookUiUrl);
          return Promise.resolve();
        }
        return fetch(this.psFacebookRetrieveExternalBusinessId, {
          method: 'POST',
          headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        }).then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        }).then((res) => {
          if (!res.externalBusinessId) {
            throw new Error('Cannot retrieve ExternalBusinessId.');
          }
          this.dynamicExternalBusinessId = res.externalBusinessId;
          this.openPopup = generateOpenPopup(this, this.psFacebookUiUrl);
          this.$root.refreshExternalBusinessId(res.externalBusinessId);
        });
      }

      this.openPopup = generateOpenPopup(this, this.psFacebookUiUrl);
      return Promise.resolve();
    },
    createExternalBusinessIdAndOpenPopup() {
      if (this.psFacebookRetrieveExternalBusinessId) {
        this.createExternalBusinessId().then(() => {
          this.openedPopup = this.openPopup();
        }).catch((error) => {
          console.error(error);
          this.error = 'configuration.messages.unknownOnboardingError';
          this.showGlass = false;
          this.openedPopup = null;
          this.popupReceptionDuplicate = false;
          this.$forceUpdate();
        });
      }
    },
    glassClicked() {
      if (this.openedPopup) {
        this.openedPopup.focus();
      } else {
        this.openedPopup = this.openPopup();
      }
      this.$segment.track('Click on black screen', {
        module: 'ps_facebook',
      });
    },
    closePopup(event) {
      event.stopPropagation(); // avoid popup to be focused before close
      if (this.openedPopup) {
        this.openedPopup.close();
      }
      this.$segment.track('Click on the cross to close the pop-in', {
        module: 'ps_facebook',
      });
    },
    md2html: (md) => (new Showdown.Converter()).makeHtml(md),
  },
  watch: {
    contextPsAccounts() {
      this.$forceUpdate();
    },
    contextPsFacebook(newValue) {
      const oldValue = this.dynamicContextPsFacebook;
      if (oldValue && !oldValue.email && newValue && newValue.email) {
        this.psFacebookJustOnboarded = true;
      }
      this.dynamicContextPsFacebook = newValue;
      this.$forceUpdate();
    },
  },
});
</script>

<style lang="scss">
  .ps-facebook-configuration-tab {
    div.card:not(.survey) {
      border: none !important;
      border-radius: 3px;
    }
  }
</style>
<style lang="scss" scoped>
  .glass {
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 10000;

    & > .refocus {
      text-align: center;
      margin-top: 25vh;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;

      & > img {
        box-shadow: 3px 5px 10px rgba(0, 0, 0, 0.1);
        border-radius: 50%;
      }

      & > p, & > a {
        font-family: "Open Sans";
        font-size: 14px;
        letter-spacing: 0;
        line-height: 21px;
        text-align: center;
        text-shadow: 3px 5px 10px rgba(0,0,0,0.4);
        max-width: 460px;
      }

      & > a {
        font-weight: 600;
      }
    }
    & > .closeCross {
      position: fixed;
      right: 0;
      top: 0;
      color: white;
      text-shadow: 3px 5px 10px rgba(0,0,0,.4);

      & > i {
        color: #fff;
        font-size: 2.6em !important;
      }
    }
  }
</style>
