<!--**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div
    id="configuration"
    class="ps-facebook-configuration-tab"
  >
    <introduction
      v-if="!psAccountsOnboarded && showIntroduction"
      @onHide="showIntroduction = false"
      class="m-4"
    />
    <template v-else>
      <messages
        :show-onboard-succeeded="psFacebookJustOnboarded"
        :show-sync-catalog-advice="psAccountsOnboarded && showSyncCatalogAdvice"
        :error="error"
        @onSyncCatalogAdviceClick="onSyncCatalogAdviceClick"
        class="m-4"
      />
      <ps-accounts
        :context="contextPsAccounts"
        class="m-4"
      />

      <no-config
        v-if="!psAccountsOnboarded"
        class="m-4"
      />
      <template v-else>
        <facebook-not-connected
          v-if="!facebookConnected"
          @onFbeOnboardClick="onFbeOnboardClick"
          class="m-4"
        />
        <facebook-connected
          v-else
          :context-ps-facebook="dynamicContextPsFacebook"
          @onEditClick="onEditClick"
          @onPixelActivation="onPixelActivation"
          class="m-4"
        />
        <div
          v-if="showGlass"
          class="glass"
        />
      </template>
    </template>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {PsAccounts} from 'prestashop_accounts_vue_components';
import Introduction from '../components/configuration/introduction.vue';
import Messages from '../components/configuration/messages.vue';
import NoConfig from '../components/configuration/no-config.vue';
import FacebookConnected from '../components/configuration/facebook-connected.vue';
import FacebookNotConnected from '../components/configuration/facebook-not-connected.vue';
import openPopupGenerator from '../lib/fb-login';

const generateOpenPopup = (component, popupUrl) => {
  const canGeneratePopup = (
    component.contextPsAccounts.currentShop
    && component.contextPsAccounts.currentShop.url
    && component.externalBusinessId
    && component.psAccountsToken
  );
  return canGeneratePopup ? openPopupGenerator(
    window,
    component.contextPsAccounts.currentShop.url.replace(/^(https?:\/\/[^/]+)(.*)/, '$1'),
    popupUrl,
    '/index.html',
    component.contextPsAccounts.currentShop.name || 'Unnamed PrestaShop shop',
    component.externalBusinessId,
    component.psAccountsToken,
    component.currency,
    component.timezone,
    component.locale,
    null,
    component.onFbeOnboardOpened,
    component.onFbeOnboardClosed,
    component.onFbeOnboardResponded,
  ) : () => {};
};

export default defineComponent({
  name: 'Configuration',
  components: {
    Introduction,
    Messages,
    PsAccounts,
    NoConfig,
    FacebookNotConnected,
    FacebookConnected,
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
      default: () => global.contextPsFacebook,
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
      required: true,
      default: () => global.psFacebookPixelActivationRoute || null,
    },
    fbeOnboardingSaveRoute: {
      type: String,
      required: true,
      default: () => global.psFacebookFbeOnboardingSaveRoute || null,
    },
    psFacebookUiUrl: {
      type: String,
      required: true,
      default: () => global.psFacebookFbeUiUrl || null,
    },
  },
  computed: {
    psAccountsOnboarded() {
      return this.contextPsAccounts.user.email !== null
        && this.contextPsAccounts.user.emailIsValidated;
    },
    facebookConnected() {
      return (this.dynamicContextPsFacebook && this.dynamicContextPsFacebook.email) || false;
    },
  },
  data() {
    return {
      dynamicContextPsFacebook: this.contextPsFacebook,
      showIntroduction: true, // Initialized to true except if a props should avoid the introduction
      psFacebookJustOnboarded: false, // Put this to true just after FBE onboarding is finished once
      showSyncCatalogAdvice: this.contextPsFacebook
        && this.contextPsFacebook.categoriesMatching
        && this.contextPsFacebook.categoriesMatching.sent !== true,
      openPopup: generateOpenPopup(this, this.psFacebookUiUrl),
      showGlass: false,
      error: null,
    };
  },
  methods: {
    onSyncCatalogAdviceClick() {
      this.$router.push({name: 'Catalog', query: {component: 'matching'}});
    },
    onFbeOnboardClick() {
      this.openPopup();
    },
    onEditClick() {
      this.openPopup();
    },
    onPixelActivation() {
      const actualState = this.dynamicContextPsFacebook.pixel.activated;
      const newState = !actualState;

      // Save activation state in PHP side.
      fetch(this.pixelActivationRoute, {
        method: 'POST',
        // mode: 'cors', // no-cors, *cors, same-origin
        // cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        // credentials: 'same-origin', // include, *same-origin, omit
        // referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade,
        //   origin, origin-when-cross-origin, same-origin, strict-origin,
        //   strict-origin-when-cross-origin, unsafe-url
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({pixelActivation: newState}), // TODO !0: format to see with Pablo PR
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        this.dynamicContextPsFacebook = {
          ...this.dynamicContextPsFacebook,
          pixel: {...this.dynamicContextPsFacebook.pixel, activated: newState},
        };
      }).catch((error) => {
        console.error(error);
        this.error = 'configuration.messages.unknownOnboardingError';
        this.dynamicContextPsFacebook = {
          ...this.dynamicContextPsFacebook,
          pixel: {...this.dynamicContextPsFacebook.pixel, activated: actualState},
        };
      });
    },
    onFbeOnboardOpened() {
      this.showGlass = true;
    },
    onFbeOnboardClosed() {
      this.showGlass = false;
    },
    onFbeOnboardResponded(response) {
      // console.log('response received', response);
      if (!response.access_token) {
        this.showGlass = false;
        return;
      }
      this.showGlass = true;

      // Save access_token, fbe?, and more on PHP side. And gets back contextPsFacebook object in response.
      fetch(this.fbeOnboardingSaveRoute, {
        method: 'POST',
        // mode: 'cors', // no-cors, *cors, same-origin
        // cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        // credentials: 'same-origin', // include, *same-origin, omit
        // referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade,
        //   origin, origin-when-cross-origin, same-origin, strict-origin,
        //   strict-origin-when-cross-origin, unsafe-url
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({onboarding: response}), // TODO !0: format to see
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        const fakeContextPsFacebook = {}; // TODO !0: get all missing data from res.json()
        this.dynamicContextPsFacebook = fakeContextPsFacebook;
        this.error = 'configuration.messages.unknownOnboardingError';
        this.showGlass = false;
      }).catch((error) => {
        console.error(error);
        this.error = 'configuration.messages.unknownOnboardingError';
        this.showGlass = false;
        this.$forceUpdate();
      });
    },
  },
  watch: {
    contextPsAccounts() {
      this.$forceUpdate();
    },
    contextPsFacebook(newValue) {
      this.dynamicContextPsFacebook = newValue;
      this.$forceUpdate();
    },
  },
});
</script>

<style lang="scss">
  .ps-facebook-configuration-tab {
    div.card {
      border: none;
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
  }
</style>
