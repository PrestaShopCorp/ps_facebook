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
  <div>
    <spinner v-if="loading && !showPopupGlass && !showTokensGlass" />
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
          :alert-settings="alertSettings"
          @onSyncCatalogClick="onSyncCatalogClick"
          @onCategoryMatchingClick="onCategoryMatchingClick"
          @onAdCampaignClick="onAdCampaignClick"
          class="m-3"
        />
        <ModuleActionNeeded
          module-name="Accounts"
          :module-version-check="psAccountsVersionCheck"
        />
        <ModuleActionNeeded
          module-name="EventBus"
          :module-version-check="psEventBusVersionCheck"
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
          :is-module-enabled="isModuleEnabled"
          @onEditClick="onEditClick"
          @onPixelActivation="onPixelActivation"
          @onUninstallClick="onUninstallClick"
          class="m-3"
        />
        <survey v-if="facebookConnected" />
        <div
          v-if="showPopupGlass"
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
        <div
          v-if="showTokensGlass"
          class="glass"
        >
          <div
            v-if="exchangeTokensErrored"
            class="refocus"
          >
            <i class="material-icons fixed-size-large mb-2">warning</i>
            <p>{{ $t('configuration.facebook.exchangeTokens.errored') }}</p>
            <b-button
              variant="primary"
              class="mt-2 error-button"
              @click="onFbeTokensExchangeFailedConfirm"
            >
              {{ $t('configuration.facebook.exchangeTokens.understood') }}
            </b-button>
          </div>
          <div
            v-else
            class="refocus"
          >
            <h2>
              {{ $t('configuration.facebook.exchangeTokens.almostThere') }}
            </h2>
            <p>
              {{ $t('configuration.facebook.exchangeTokens.acknowledging') }}
              <br>
              {{ $t('configuration.facebook.exchangeTokens.takesTime') }}
            </p>
            <span class="exchangeTokensLoader"><spinner /></span>
            <p v-if="exchangeTokensTryAgain">
              <i class="material-icons float-left fixed-size-big">warning</i>
              {{ $t('configuration.facebook.exchangeTokens.tryAgain') }}
            </p>
          </div>
        </div>
      </template>
    </div>

    <!-- Confirmation modal for FBE uninstallation -->
    <div
      id="ps_facebook_modal_unlink"
      class="modal"
    >
      <div
        class="modal-dialog"
        role="document"
      >
        <div class="modal-content tw-rounded-none">
          <div class="modal-header">
            <slot name="header">
              <div class="tw-flex tw-items-center">
                <h5 class="modal-title tw-pl-3">
                  {{ $t('configuration.facebook.connected.unlinkModalHeader') }}
                </h5>
              </div>
            </slot>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            {{ $t('configuration.facebook.connected.unlinkModalText') }}
          </div>
          <div class="modal-footer">
            <b-button
              variant="primary"
              target="_blank"
              data-dismiss="modal"
              @click="onUninstallClick"
            >
              {{ $t('integrate.buttons.modalConfirm') }}
            </b-button>
          </div>
        </div>
      </div>
    </div>
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
import ModuleActionNeeded from '../components/warning/module-action-needed.vue';

const generateOpenPopup = window.psFacebookGenerateOpenPopup || ((component, popupUrl) => {
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
});

export default defineComponent({
  name: 'Configuration',
  components: {
    Spinner,
    Introduction,
    Messages,
    MultiStoreSelector,
    PsAccounts,
    ModuleActionNeeded,
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
    isModuleEnabled: {
      type: Boolean,
      required: false,
      default: () => global.psFacebookModuleEnabled ?? true,
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
    ensureTokensExchangedRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookEnsureTokensExchanged || null,
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
    psAccountsVersionCheck: {
      type: Object,
      required: false,
      default: () => global.psAccountsVersionCheck,
    },
    psEventBusVersionCheck: {
      type: Object,
      required: false,
      default: () => global.psEventBusVersionCheck,
    },
    retrieveTokensRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookRetrieveTokensRoute || null,
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
  },
  data() {
    return {
      dynamicContextPsFacebook: this.contextPsFacebook,
      dynamicExternalBusinessId: this.externalBusinessId,
      showIntroduction: true, // Initialized to true except if a props should avoid the introduction
      psFacebookJustOnboarded: false, // Put this to true just after FBE onboarding is finished once
      openPopup: generateOpenPopup(this, this.psFacebookUiUrl),
      showPopupGlass: false,
      showTokensGlass: false,
      alertSettings: {},
      loading: true,
      popupReceptionDuplicate: false,
      openedPopup: null,
      shops: this.contextPsAccounts.shops || [],
      exchangeTokensTryAgain: false,
      exchangeTokensErrored: false,
    };
  },
  created() {
    if ((!this.contextPsFacebook || !this.externalBusinessId)
      && global.psFacebookGetFbContextRoute) {
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
            return res.json().then((errors) => { throw errors; })
              .catch(() => {
                throw new Error(res.statusText || res.status);
              });
          }
          return res.json();
        })
        .then((json) => {
          this.$root.refreshContextPsFacebook(json.contextPsFacebook);
          this.dynamicExternalBusinessId = json.psFacebookExternalBusinessId;
          this.createExternalBusinessId();

          return this.fetchTokens()
            .then(() => {
              this.loading = false;
            });
        }).catch((error) => {
          this.loading = false;
          this.setErrorsFromFbCall(error);
        });
    },
    fetchTokens() {
      if (!this.facebookConnected) {
        return Promise.resolve();
      }

      const suspendedHandler = (res) => {
        if (!res || !res.suspensionReason) {
          return;
        }
        console.error('Your account is suspended:', res.suspensionReason);
        this.setErrorsFromFbCall({reason: 'configuration.messages.accountSuspended'});
      };

      if (!this.retrieveTokensRoute) {
        return Promise.resolve().then(
          suspendedHandler.bind(this, {suspensionReason: 'Suspension status cannot be fetched!'}),
        );
      }

      // We don't care about tokens, we just need to know if suspension is true...
      return fetch(this.retrieveTokensRoute, {
        method: 'GET',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then(suspendedHandler.bind(this)).catch((error) => {
        console.error(error);
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
      this.showPopupGlass = true;
    },
    onEditClick() {
      this.openedPopup = this.openPopup();
      this.showPopupGlass = true;
    },
    onUninstallClick() {
      this.loading = true;
      // FIXME: Duplicated by ensureTokensExchanged()
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
          this.psFacebookJustOnboarded = false;
        }).catch((error) => {
          console.error(error);
          this.setErrorsFromFbCall(error);
        });

      this.$segment.track('Click on unlink', {
        module: 'ps_facebook',
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
        this.loading = false;
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (!res.success) {
          throw new Error('Error');
        }
        this.$root.refreshContextPsFacebook({
          ...this.dynamicContextPsFacebook,
          pixel: {...this.dynamicContextPsFacebook.pixel, isActive: newState},
        });
      }).catch((error) => {
        console.error(error);
        this.setErrorsFromFbCall(error);
        this.$root.refreshContextPsFacebook({
          ...this.dynamicContextPsFacebook,
          pixel: {...this.dynamicContextPsFacebook.pixel, isActive: actualState},
        });
      });
    },
    onFbeOnboardOpened() {
      this.showPopupGlass = true;
    },
    onFbeOnboardClosed() {
      this.showPopupGlass = false;
      this.openedPopup = null;
    },
    onFbeOnboardResponded(response, save = this.saveFbeOnboarding.bind(this)) {
      if (this.popupReceptionDuplicate) {
        console.log('duplicated response received');
        return;
      }
      this.popupReceptionDuplicate = true;
      console.log('response received', response);

      if (!response.access_token) {
        this.showPopupGlass = false;
        this.openedPopup = null;
        return;
      }
      this.loading = true;
      this.showTokensGlass = true;

      save(response).then(() => {
        this.openedPopup = null;
        this.alertSettings = {};
        this.popupReceptionDuplicate = false;

        // after successfully onboarded, wait a few seconds,
        // then call exchange tokens route to force system token to be retrieved.
        setTimeout(this.ensureTokensExchanged.bind(this, true), 5000);
      }).catch((error) => {
        console.error('The pop-up gets blocked');
        console.error(error);
        if (this.$segment) {
          this.$segment.track('The pop-up gets blocked', {
            module: 'ps_facebook',
          });
        }
        this.setErrorsFromFbCall(error);
        this.loading = false;
        this.showTokensGlass = false;
        this.openedPopup = null;
        this.popupReceptionDuplicate = false;
        this.$forceUpdate();
      });
    },
    saveFbeOnboarding(response) {
      // Save access_token, fbe?, and more on PHP side. And gets back contextPsFacebook in response.
      return fetch(this.fbeOnboardingSaveRoute, {
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
        console.log('Onboarding successfull on PrestaShop side.');
        if (this.$segment) {
          this.$segment.track('PS Account & FBE connected', {
            module: 'ps_facebook',
          });
        }
        this.$root.refreshContextPsFacebook(res.contextPsFacebook);
      });
    },
    ensureTokensExchanged(tryAgainOnError = false) {
      this.exchangeTokensTryAgain = false;
      this.exchangeTokensErrored = false;
      fetch(this.ensureTokensExchangedRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res2) => {
        if (!res2.ok) {
          throw new Error(res2.statusText || res2.status);
        }
        return res2.json();
      }).then((res2) => {
        if (!res2.success) {
          throw new Error('Error!');
        }
        console.log('Tokens exchanged - FBE fully onboarded.');
        if (this.$segment) {
          this.$segment.track('FBE fully onboarded', {
            module: 'ps_facebook',
          });
        }
        this.loading = false;
        this.showTokensGlass = false;
        this.psFacebookJustOnboarded = true;
      }).catch((error) => {
        console.error('Tokens exchange failure. Please refresh the page, or you will need to onboard again in 1 hour.');
        console.error(error);

        if (tryAgainOnError) {
          console.log('We will try again in 10 seconds...');
          this.exchangeTokensTryAgain = true;
          setTimeout(this.ensureTokensExchanged.bind(this, false), 10000);
        } else {
          // failure, force un-onboarding
          console.log('Exchange tokens failed, un-onboard now...');
          if (this.$segment) {
            this.$segment.track('Exchange tokens failed, un-onboard now', {
              module: 'ps_facebook',
            });
          }
          // FIXME: Duplicates content of this.onUninstallClick()
          fetch(this.fbeOnboardingUninstallRoute)
            .then((res) => {
              this.exchangeTokensErrored = true;
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
            }).catch((err) => {
              console.error(err);
              this.setErrorsFromFbCall(err);
            });
        }
      });
    },
    onFbeTokensExchangeFailedConfirm() {
      this.exchangeTokensErrored = false;
      this.loading = false;
      this.showTokensGlass = false;
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
          console.log('ExternalBusinessId:', this.dynamicExternalBusinessId);
          if (this.$store.state) {
            console.log('ShopId:', this.$store.state.context.appContext.shopId);
          }
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
          console.log('ExternalBusinessId:', this.dynamicExternalBusinessId);
          if (this.$store.state) {
            console.log('ShopId:', this.$store.state.context.appContext.shopId);
          }
          this.openPopup = generateOpenPopup(this, this.psFacebookUiUrl);

          this.$root.refreshExternalBusinessId(res.externalBusinessId);
          this.$root.identifySegment();
        }).catch((error) => {
          this.setErrorsFromFbCall(error);
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
          this.setErrorsFromFbCall(error);
          this.showPopupGlass = false;
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
      if (this.$segment) {
        this.$segment.track('Click on black screen', {
          module: 'ps_facebook',
        });
      }
    },
    closePopup(event) {
      event.stopPropagation(); // avoid popup to be focused before close
      if (this.openedPopup) {
        this.openedPopup.close();
      }
      if (this.$segment) {
        this.$segment.track('Click on the cross to close the pop-in', {
          module: 'ps_facebook',
        });
      }
    },
    setErrorsFromFbCall(error) {
      this.alertSettings = {
        error: error?.reason || 'configuration.messages.unknownOnboardingError',
        errorButton: error.reason ? 'configuration.facebook.notConnected.connectButton' : 'configuration.messages.reloadButton',
      };
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

      & > h2 {
        color: white !important;
        font-family: "Open Sans";
        font-size: 18px;
        letter-spacing: 0;
        line-height: 21px;
        text-align: center;
        text-shadow: 3px 5px 10px rgba(0,0,0,0.4);
        max-width: 460px;
      }

      & > a {
        font-weight: 600;
      }

      & > span {
        text-align: center;

        & .spinner {
          top: 3rem;
        }
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

    & .exchangeTokensLoader {
      margin-bottom: 6rem;

      & > div {
        box-shadow: 3px 5px 10px rgba(0, 0, 0, 0.1);
      }
    }

    & .fixed-size-large {
      font-size: 64pt;
      text-shadow: 3px 5px 10px rgba(0,0,0,.1);
    }

    & .fixed-size-big {
      font-size: 30pt;
      text-shadow: 3px 5px 10px rgba(0,0,0,.1);
    }

    & .error-button {
      box-shadow: 3px 5px 10px rgba(0, 0, 0, 0.1);
    }
  }
</style>
