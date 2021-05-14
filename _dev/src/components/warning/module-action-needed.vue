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
    v-if="!actionNeededDone && actionNeeded"
  >
    <div>
      <p
        v-html="md2html(
          $t(`configuration.messages.${moduleName}${actionNeeded}NeededWarning`, this)
        )"
      />
      <div
        v-if="loading"
        class="spinner mt-2"
      />
      <b-button
        v-else
        variant="primary"
        class="mt-2"
        @click="onActionClick"
      >
        {{ $t(`configuration.messages.${moduleName}${actionNeeded}Button`) }}
      </b-button>
    </div>
  </b-alert>
  <b-alert
    v-else-if="actionNeededDone"
    variant="success"
    class="m-3"
    show
    dismissible
  >
    {{ $t(`configuration.messages.${moduleName}${actionNeeded}Done`) }}
  </b-alert>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import Showdown from 'showdown';
import {
  BAlert,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'ModuleUpdateNeeded',
  components: {
    BAlert,
  },
  mixins: [],
  data() {
    return {
      actionNeededDone: false,
      loading: false,
    };
  },
  props: {
    moduleName: {
      type: String,
      required: true,
    },
    moduleVersionCheck: {
      type: Object,
      required: true,
    },
  },
  computed: {
    actionNeeded() {
      if (this.moduleVersionCheck.needsUpgrade) {
        return 'Upgrade';
      }
      if (this.moduleName === 'Accounts') {
        // No need to check ps_accounts is enabled & installed,
        // as it is handled by the libraries
        return null;
      }
      if (this.moduleVersionCheck.needsInstall) {
        return 'Install';
      }
      if (this.moduleVersionCheck.needsEnable) {
        return 'Enable';
      }
      return null;
    },
    actionRoute() {
      if (this.actionNeeded === 'Upgrade') {
        return this.moduleVersionCheck.psFacebookUpgradeRoute;
      }
      if (this.actionNeeded === 'Install') {
        return this.moduleVersionCheck.psFacebookInstallRoute;
      }
      if (this.actionNeeded === 'Enable') {
        return this.moduleVersionCheck.psFacebookEnableRoute;
      }
      return null;
    },
    // Specific to the upgrade
    currentVersion() {
      return this.moduleVersionCheck.currentVersion || null;
    },
    requiredVersion() {
      return this.moduleVersionCheck.requiredVersion || null;
    },
  },
  methods: {
    md2html: (md) => (new Showdown.Converter()).makeHtml(md),
    onActionClick() {
      this.loading = true;

      fetch(this.actionRoute, {
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
        this.actionNeededDone = true;
        this.$segment.track(`${this.moduleName} upgraded from alert`, {
          module: 'ps_facebook',
        });
      }).catch((error) => {
        console.error(error);
        this.loading = false;
        this.$segment.track(`Upgrade of ${this.moduleName} failed`, {
          module: 'ps_facebook',
        });
      });
    },
  },
});
</script>
