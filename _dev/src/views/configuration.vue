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
      // TODO !1: show if (this.facebookConnected AND categories matching is not done yet)
      showSyncCatalogAdvice: false,
    };
  },
  methods: {
    onSyncCatalogAdviceClick() {
      // TODO !0: what feature ??? should go to corresponding Tab (use VueJS router ?)
    },
    onFbeOnboardClick() {
      // TODO !0: launch FBE onboarding
    },
    onEditClick() {
      // TODO !0: RE-launch FBE onboarding ?
    },
    onPixelActivation() {
      // TODO !1
    },
  },
  watch: { }, // TODO !1: does the props.contextPsFacebook can change in time?
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
