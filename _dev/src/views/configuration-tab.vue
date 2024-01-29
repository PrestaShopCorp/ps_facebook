<template>
  <div>
    <loading-page-spinner v-if="loading && !showPopupGlass && !showTokensGlass" />
    <div
      id="configuration"
      class="ps-facebook-configuration-tab"
      v-else
    >
      <MultiStoreSelector
        v-if="!contextPsAccounts.isShopContext && shops.length"
        :shops="shops"
        class="m-3"
        @shop-selected="onShopSelected($event)"
      />
      <template v-else>
        <messages-container
          :show-onboard-succeeded="psFacebookJustOnboarded"
          :alert-settings="alertSettings"
          class="m-3"
        />
        <ModuleActionNeeded
          module-name="Accounts"
          :module-version-check="psAccountsVersionCheck"
        />
        <ModuleActionNeeded
          module-name="EventBus"
          :module-version-check="psCloudSyncVersionCheck"
        />
        <banner-catalog-sharing
          v-if="facebookConnected && catalogDataLoaded && !GET_CATALOG_PAGE_ENABLED"
          :on-catalog-page="false"
          class="m-3"
        />
        <b-alert
          v-if="psAccountShopInConflict"
          variant="danger"
          class="m-3"
          show
        >
          <p
            v-html="md2html($t('configuration.messages.shopInConflictError'))"
          />
        </b-alert>
        <onboarding-deps-container
          v-else
          :ps-accounts-onboarded="psAccountsOnboarded"
          :billing-running="GET_BILLING_SUBSCRIPTION_ACTIVE"
          :facebook-onboarded="IS_USER_ONBOARDED"
          @onCloudsyncConsentUpdated="cloudSyncSharingConsentGiven = $event"
          class="m-3"
        />

        <div
          class="m-3"
          v-if="GET_BILLING_SUBSCRIPTION_ACTIVE"
        >
          <two-panel-cols
            :title="$t('configuration.sectionTitle.pssocial')"
            :description="$t('configuration.sectionDesc.pssocial')"
          >
            <facebook-not-connected
              v-if="!facebookConnected"
              @onFbeOnboardClick="onFbeOnboardClick"
              :active="psAccountsOnboarded && cloudSyncSharingConsentGiven"
              :can-connect="!!externalBusinessId"
              :encourage-to-retry="encourageToRetry"
            />
            <facebook-connected
              v-else
              :ps-facebook-app-id="psFacebookAppId"
              :external-business-id="externalBusinessId"
              :context-ps-facebook="contextPsFacebook"
              :is-module-enabled="isModuleEnabled"
              @onEditClick="onEditClick"
              @onPixelActivation="onPixelActivation"
              @onFbeUnlinkRequest="onFbeUnlinkRequest"
              @onUninstallClick="onUninstallClick"
            />
          </two-panel-cols>
        </div>
        <key-features
          class="m-3"
          v-else
        />

        <card-survey v-if="facebookConnected" />
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
              alt="PrestaShop Social logo"
            >
            <p>{{ $t('configuration.glass.text') }}</p>
            <b-button
              class="ps_gs-glass__button mt-3"
              variant="outline-secondary"
              size="sm"
              @click="glassClicked"
            >
              {{ $t('configuration.glass.link') }}
            </b-button>
          </div>
          <div
            class="closeCross p-1 m-4"
            @click="closePopup"
          >
            <i class="material-icons-round">close</i>
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
            <i class="material-icons-round fixed-size-large mb-2">warning</i>
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
            <span class="exchangeTokensLoader"><loading-page-spinner /></span>
            <p v-if="exchangeTokensTryAgain">
              <i class="material-icons-round float-left fixed-size-big">warning</i>
              {{ $t('configuration.facebook.exchangeTokens.tryAgain') }}
            </p>
          </div>
        </div>
      </template>
    </div>

    <!-- Confirmation modal for FBE uninstallation -->
    <ps-modal
      id="ps_facebook_modal_unlink"
      ref="ps_facebook_modal_unlink"
      :title="$t('configuration.facebook.connected.unlinkModalHeader')"
      @ok="onUninstallClick"
      ok-variant="danger"
    >
      {{ $t('configuration.facebook.connected.unlinkModalText') }}
      <template slot="modal-cancel">
        {{ $t('cta.cancel') }}
      </template>
      <template slot="modal-ok">
        {{ $t('cta.unlink') }}
      </template>
    </ps-modal>

    <modal-configuration-completed
      v-if="psFacebookJustOnboarded"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {mapGetters} from 'vuex';
import Showdown from 'showdown';
import MultiStoreSelector from '@/components/multistore/multi-store-selector.vue';
import PsModal from '@/components/commons/ps-modal.vue';
import LoadingPageSpinner from '@/components/spinner/loading-page-spinner.vue';
import MessagesContainer from '../components/configuration/messages-container.vue';
import FacebookConnected from '../components/configuration/facebook-connected.vue';
import FacebookNotConnected from '../components/configuration/facebook-not-connected.vue';
import OnboardingDepsContainer from '@/components/configuration/onboarding-deps-container.vue';
import CardSurvey from '@/components/survey/card-survey.vue';
import openPopupGenerator from '../lib/fb-login';
import ModuleActionNeeded from '../components/warning/module-action-needed.vue';
import {OnboardingContext} from '@/store/modules/onboarding/state';
import TwoPanelCols from '@/components/configuration/two-panel-cols.vue';
import KeyFeatures from '@/components/configuration/key-features.vue';
import ModalConfigurationCompleted from '@/components/configuration/modal-configuration-completed.vue';
import BannerCatalogSharing from '@/components/catalog/summary/banner-catalog-sharing.vue';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';
import GettersTypesCatalog from '@/store/modules/catalog/getters-types';
import GettersTypesApp from '@/store/modules/app/getters-types';
import {RequestState} from '@/store/types';

const generateOpenPopup: () => () => Window|null = window.psFacebookGenerateOpenPopup || (
  (component, popupUrl: string) => {
    const canGeneratePopup = (
      component.contextPsAccounts.currentShop
      && component.contextPsAccounts.currentShop.url
      && component.externalBusinessId
      && component.psAccountsToken
    );

    if (!canGeneratePopup) {
      return () => null;
    }

    return openPopupGenerator(
      window,
      component.contextPsAccounts.currentShop.url.replace(/^(https?:\/\/[^/]+)(.*)/, '$1'),
      popupUrl,
      '/index.html',
      component.contextPsAccounts.currentShop.name || 'Unnamed PrestaShop shop',
      component.contextPsAccounts.currentShop.frontUrl,
      component.externalBusinessId,
      component.psAccountsToken,
      component.currency,
      component.timezone,
      component.locale,
      null,
      component.onFbeOnboardOpened,
      component.onFbeOnboardClosed,
      component.onFbeOnboardResponded,
    );
  });

export default defineComponent({
  name: 'ConfigurationTab',
  components: {
    BannerCatalogSharing,
    CardSurvey,
    FacebookNotConnected,
    FacebookConnected,
    KeyFeatures,
    LoadingPageSpinner,
    MessagesContainer,
    ModalConfigurationCompleted,
    ModuleActionNeeded,
    MultiStoreSelector,
    OnboardingDepsContainer,
    PsModal,
    TwoPanelCols,
  },
  mixins: [],
  props: {
    contextPsAccounts: {
      type: Object,
      required: false,
      default: () => window.contextPsAccounts,
    },
    psFacebookAppId: {
      type: String,
      required: false,
      default: () => window.psFacebookAppId,
    },
    psAccountsToken: {
      type: String,
      required: false,
      default: () => window.psAccountsToken,
    },
    psAccountShopInConflict: {
      type: Boolean,
      required: false,
      default: () => window.psAccountShopInConflict,
    },
    currency: {
      type: String,
      required: false,
      default: () => window.psFacebookCurrency || 'EUR',
    },
    timezone: {
      type: String,
      required: false,
      default: () => window.psFacebookTimezone || 'Europe/Paris',
    },
    isModuleEnabled: {
      type: Boolean,
      required: false,
      default: () => window.psFacebookModuleEnabled ?? true,
    },
    locale: {
      type: String,
      required: false,
      default: () => window.psFacebookLocale || 'en-US',
    },
    pixelActivationRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookPixelActivationRoute || null,
    },
    fbeOnboardingSaveRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookFbeOnboardingSaveRoute || null,
    },
    ensureTokensExchangedRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookEnsureTokensExchanged || null,
    },
    fbeOnboardingUninstallRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookFbeOnboardingUninstallRoute || null,
    },
    psFacebookUiUrl: {
      type: String,
      required: false,
      default: () => window.psFacebookFbeUiUrl || null,
    },
    psAccountsVersionCheck: {
      type: Object,
      required: false,
      default: () => window.psAccountsVersionCheck,
    },
    psCloudSyncVersionCheck: {
      type: Object,
      required: false,
      default: () => window.psCloudSyncVersionCheck || window.psEventBusVersionCheck,
    },
    retrieveTokensRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookRetrieveTokensRoute || null,
    },
  },
  computed: {
    ...mapGetters('catalog', [
      GettersTypesCatalog.GET_CATALOG_PAGE_ENABLED,
    ]),
    ...mapGetters('app', [
      GettersTypesApp.GET_BILLING_SUBSCRIPTION_ACTIVE,
    ]),
    ...mapGetters('onboarding', [
      GettersTypesOnboarding.IS_USER_ONBOARDED,
    ]),

    psAccountsOnboarded() {
      return this.contextPsAccounts.user
        && this.contextPsAccounts.user.email !== null
        && this.contextPsAccounts.user.emailIsValidated;
    },
    facebookConnected(): boolean {
      return (this.GET_BILLING_SUBSCRIPTION_ACTIVE
        && !!this.contextPsFacebook?.facebookBusinessManager?.id);
    },
    openPopup(): () => Window|null {
      if (!this.externalBusinessId) {
        return () => null;
      }
      return generateOpenPopup(this, this.psFacebookUiUrl);
    },
    externalBusinessId(): string|null {
      return this.$store.getters['onboarding/GET_EXTERNAL_BUSINESS_ID'];
    },
    contextPsFacebook(): OnboardingContext|null {
      return this.$store.getters['onboarding/GET_ONBOARDING_STATE'];
    },
    safePopup(): Window|null {
      // The popup can be unreachable if closed of blocked by CORS policy.
      // We return it only when it is safe to do so (= allowed by the browser).
      try {
        if (!this.popup) {
          return null;
        }
        // This should trigger an error in case of CORS.
        this.popup.dispatchEvent(new Event('ping'));
        return this.popup;
      } catch (error) {
        console.log('error', error instanceof DOMException);
        if (error instanceof DOMException) {
          return null;
        }
        throw error;
      }
    },
    catalogDataLoaded(): boolean {
      return this.$store.state.catalog.warmedUp === RequestState.SUCCESS;
    },
  },
  data() {
    return {
      psFacebookJustOnboarded: false, // Put this to true just after FBE onboarding is finished once
      showPopupGlass: false,
      showTokensGlass: false,
      alertSettings: {},
      loading: true,
      popupReceptionDuplicate: false,
      popup: null as Window|null,
      shops: this.contextPsAccounts.shops || [],
      exchangeTokensTryAgain: false,
      exchangeTokensErrored: false,
      cloudSyncSharingConsentGiven: false,
      encourageToRetry: false as boolean,
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
    async fetchData() {
      this.loading = true;
      try {
        await this.$store.dispatch('onboarding/WARMUP_STORE');
        await this.fetchTokens();
      } catch (error) {
        this.setErrorsFromFbCall(error);
      } finally {
        this.loading = false;
      }
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
    onFbeOnboardClick() {
      this.popup = this.openPopup();
      this.showPopupGlass = true;
    },
    onEditClick() {
      this.popup = this.openPopup();
      this.showPopupGlass = true;
    },
    onUninstallClick() {
      this.loading = true;
      // FIXME: Duplicated by ensureTokensExchanged()
      fetch(this.fbeOnboardingUninstallRoute)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then(async () => {
          await Promise.all([
            this.$store.dispatch('onboarding/REQUEST_EXTERNAL_BUSINESS_ID'),
            this.$store.dispatch('onboarding/REQUEST_ONBOARDING_STATE'),
          ]);
          this.psFacebookJustOnboarded = false;
          this.loading = false;
          this.$store.commit('catalog/RESET');
        }).catch((error) => {
          console.error(error);
          this.setErrorsFromFbCall(error);
        });

      this.$segment.track('Click on unlink', {
        module: 'ps_facebook',
      });
    },
    onPixelActivation() {
      const actualState = this.contextPsFacebook.pixel.isActive;
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
      }).catch((error) => {
        console.error(error);
        this.setErrorsFromFbCall(error);
      });
    },
    onFbeOnboardOpened() {
      this.encourageToRetry = false;
      this.showPopupGlass = true;
    },
    onFbeOnboardClosed() {
      this.showPopupGlass = false;
      if (this.safePopup) {
        this.popup = null;
      }
      if (!this.popupReceptionDuplicate) {
        this.encourageToRetry = true;
      }
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
        if (this.safePopup) {
          this.popup = null;
        }
        return;
      }
      this.loading = true;
      this.showTokensGlass = true;

      save(response).then(() => {
        if (this.safePopup) {
          this.popup = null;
        }
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
        if (this.safePopup) {
          this.popup = null;
        }
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
        this.$store.dispatch('onboarding/REQUEST_ONBOARDING_STATE');
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
            .then(() => {
              this.$store.dispatch('onboarding/REQUEST_EXTERNAL_BUSINESS_ID');
              this.$store.dispatch('onboarding/REQUEST_ONBOARDING_STATE');
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
    onFbeUnlinkRequest() {
      this.$bvModal.show(
        this.$refs.ps_facebook_modal_unlink.$refs.modal.id,
      );
    },
    glassClicked() {
      if (this.safePopup) {
        this.safePopup.focus();
      } else {
        this.popup = this.openPopup();
      }
      if (this.$segment) {
        this.$segment.track('Click on black screen', {
          module: 'ps_facebook',
        });
      }
    },
    closePopup(event) {
      event.stopPropagation(); // avoid popup to be focused before close
      if (this.safePopup) {
        this.safePopup.close();
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
    contextPsFacebook: {
      async handler(newValue: OnboardingContext|null) {
        if (newValue) {
          await this.$store.dispatch('catalog/WARMUP_STORE');
        }
      },
      immediate: true,
    },
  },
});
</script>

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
