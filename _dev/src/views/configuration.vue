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
          :context-ps-facebook="contextPsFacebook"
          @onEditClick="onEditClick"
          @onPixelActivation="onPixelActivation"
          class="m-4"
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

const popupUrl = 'https://lui.ngrok.io'; // TODO !0: en fonction de l'env (integ, localhost, production)

const generateOpenPopup = (component) => {
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
      default: () => global.psFacebookPixelActivationRoute,
    },
    fbeOnboardingSaveRoute: {
      type: String,
      required: true,
      default: () => global.psFacebookFbeOnboardingSaveRoute,
    },
    psFacebookUiUrl: {
      type: String,
      required: true,
      default: () => global.psFacebookFbeUiUrl,
    },
  },
  computed: {
    psAccountsOnboarded() {
      return this.contextPsAccounts.user.email !== null
        && this.contextPsAccounts.user.emailIsValidated;
    },
    facebookConnected() {
      return !!this.contextPsFacebook;
    },
  },
  data() {
    return {
      showIntroduction: true, // Initialized to true except if a props should avoid the introduction
      psFacebookJustOnboarded: false, // Put this to true just after FBE onboarding is finished once
      showSyncCatalogAdvice: this.contextPsFacebook
        && this.contextPsFacebook.categoriesMatching
        && this.contextPsFacebook.categoriesMatching.sent !== true,
      openPopup: generateOpenPopup(this),
    };
  },
  methods: {
    onSyncCatalogAdviceClick() {
      // TODO !1: should go to corresponding Tab (use VueJS router to switch to categ tab)
    },
    onFbeOnboardClick() {
      this.openPopup();
    },
    onEditClick() {
      this.openPopup();
    },
    onPixelActivation() {
      // TODO !0: deja fait par Pablo
    },
    onFbeOnboardOpened() {
      console.log('Popup is opened !');
      // TODO !0: dark glass on all the document
    },
    onFbeOnboardClosed() {
      console.log('Popup is closed !');
      // TODO !0: remove dark glass
    },
    onFbeOnboardResponded(response) {
      console.log('response received', response);
      // TODO !0: check if response.access_token, sinon ne pas faire l'appel à PHP
      // TODO !0: send to PHP (ajax ? que faire du retour du call ? adapter props.contextPsFacebook ?)
    },
  },
  watch: {
    //contextPsAccounts
    //contextPsFacebook
    //externalBusinessId
    //psAccountsToken
  }, // TODO !0: these can change !
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
