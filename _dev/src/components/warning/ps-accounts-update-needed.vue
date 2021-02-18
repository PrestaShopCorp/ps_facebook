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
  <b-alert
    variant="warning"
    class="m-3"
    show
    v-if="!upgradeDone"
  >
    <div>
      <p
        v-html="md2html($t('configuration.messages.psAccountUpgradeNeededWarning', this))"
      />
      <div
        v-if="loading"
        class="spinner mt-2"
      />
      <b-button
        v-else
        variant="primary"
        class="mt-2"
        @click="onUpgradeClick"
      >
        {{ $t('configuration.messages.psAccountUpgradeButton') }}
      </b-button>
    </div>
  </b-alert>
  <b-alert
    v-else
    variant="success"
    class="m-3"
    show
    dismissible
  >
    {{ $t('configuration.messages.psAccountUpgradeDone') }}
  </b-alert>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import Showdown from 'showdown';
import {
  BAlert,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'Configuration',
  components: {
    BAlert,
  },
  mixins: [],
  data() {
    return {
      upgradeDone: false,
      loading: false,
    };
  },
  props: {
    psAccountsVersion: {
      type: String,
      required: false,
      default: () => global.psAccountVersionCheck.psAccountsVersion || null,
    },
    requiredPsAccountsVersion: {
      type: String,
      required: false,
      default: () => global.psAccountVersionCheck.requiredPsAccountsVersion || null,
    },
    psFacebookUpgradePsAccounts: {
      type: String,
      required: false,
      default: () => global.psAccountVersionCheck.psFacebookUpgradePsAccounts || null,
    },
  },
  methods: {
    md2html: (md) => (new Showdown.Converter()).makeHtml(md),
    onUpgradeClick() {
      this.loading = true;

      fetch(this.psFacebookUpgradePsAccounts, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (!res.success) {
          throw new Error('Error!');
        }
        this.loading = false;
        this.upgradeDone = true;
        this.$segment.track('PS Account upgraded from alert', {
          module: 'ps_facebook',
        });
      }).catch((error) => {
        console.error(error);
        this.loading = false;
        this.$segment.track('Upgrade of PS Accounts failed', {
          module: 'ps_facebook',
        });
      });
    },
  },
});
</script>

<style lang="scss" scoped>
</style>
