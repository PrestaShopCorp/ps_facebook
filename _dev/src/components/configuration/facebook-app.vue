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
  <div class="app pt-1 pb-3 px-2">
    <div class="text-uppercase text-muted">
      {{ appType }}
      <span v-if="!!tooltip">
        <tooltip :text="tooltip" />
      </span>
    </div>
    <img
      v-if="!!logo"
      :src="logo"
      alt="app logo"
      class="logo float-left mr-2 my-1"
    >
    <div class="font-weight-bold text-truncate">
      {{ appName }}
    </div>

    <div v-if="displayWarning">
      <warning :warning-text="$t('configuration.app.informationCannotBeDisplayedWarning')" />
    </div>
    <div v-else>
      <div
        v-if="activationSwitch != null"
        class="switchy float-right mb-1 ml-2"
      >
        <span class="d-none d-sm-inline">
          {{ statusText }}
        </span>
        <div
          class="switch-input switch-input-lg ml-1"
          :class="[
            switchActivated && !frozenSwitch ? '-checked' : null,
            frozenSwitch ? 'disabled' : null,
          ]"
          @click="switchClick"
        >
          <input
            class="switch-input-lg"
            type="checkbox"
            :checked="switchActivated && !frozenSwitch"
          >
        </div>
      </div>

      <div
        v-if="!!email"
        class="small text-truncate"
      >
        {{ email }}
      </div>
      <div
        v-if="!!appId"
        class="small text-truncate"
      >
        {{ appId }}
      </div>
      <div
        v-if="null !== likes"
        class="small"
      >
        {{ likes }}
        {{ likes >= 2 ? $t('configuration.app.likes') : $t('configuration.app.like') }}
      </div>
      <div
        v-if="!!createdAt"
        class="small"
      >
        {{ $t('configuration.app.createdAt') }}
        {{ new Date(createdAt).toLocaleDateString(undefined, { dateStyle: 'medium' }) }}
      </div>
      <div
        v-if="!!lastActive"
        class="small"
      >
        {{ $t('configuration.app.lastActive') }}
        {{ new Date(lastActive).toLocaleDateString(undefined, { dateStyle: 'medium' }) }}
        {{ new Date(lastActive).toLocaleTimeString(undefined) }}
      </div>
    </div>

    <div
      v-if="!!url"
      class="url"
    >
      <b-link
        :href="url"
        target="_blank"
        @click="onStats"
      >
        <i class="material-icons">analytics</i>
        {{ $t('configuration.app.viewStats') }} &nbsp;
        <i class="material-icons small-text">open_in_new</i>
      </b-link>
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {BFormCheckbox, BLink} from 'bootstrap-vue';
import Tooltip from '../help/tooltip.vue';
import Warning from '../warning/warning.vue';

export default defineComponent({
  name: 'FacebookApp',
  components: {
    BFormCheckbox,
    BLink,
    Tooltip,
    Warning,
  },
  props: {
    appType: {
      type: String,
      required: true,
    },
    appName: {
      type: String,
      default: null,
    },
    email: {
      type: String,
      required: false,
      default: null,
    },
    appId: {
      type: String,
      required: false,
      default: null,
    },
    likes: {
      type: Number,
      required: false,
      default: null,
    },
    url: {
      type: String,
      required: false,
      default: null,
    },
    createdAt: {
      type: String,
      required: false,
      default: null,
    },
    lastActive: {
      type: String,
      required: false,
      default: null,
    },
    activationSwitch: {
      type: Boolean,
      required: false,
      default: null,
    },
    frozenSwitch: {
      type: Boolean,
      required: false,
      default: false,
    },
    tooltip: {
      type: String,
      required: false,
      default: null,
    },
    logo: {
      type: String,
      required: false,
      default: null,
    },
    displayWarning: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      switchActivated: this.activationSwitch,
    };
  },
  computed: {
    statusText() {
      if (this.frozenSwitch) {
        return this.$t('configuration.app.moduleDisabled');
      }
      if (!this.switchActivated) {
        return this.$t('configuration.app.disabled');
      }
      return this.$t('configuration.app.activated');
    },
  },
  methods: {
    switchClick() {
      if (this.frozenSwitch) {
        return;
      }
      this.switchActivated = !this.switchActivated;
      this.$emit('onActivation', this.switchActivated);
      this.$segment.track('Click on pixel switch CTA', {
        module: 'ps_facebook',
      });
      this.$segment.track(`Feature Pixel ${this.switchActivated ? 'enabled' : 'disabled'}`, {
        module: 'ps_facebook',
      });
    },
    onStats() {
      this.$segment.track('Click on view stat CTA', {
        module: 'ps_facebook',
      });
    },
  },
  watch: {
    activationSwitch(newValue) {
      this.switchActivated = newValue;
    },
  },
});
</script>

<style lang="scss" scoped>
  .app {
    background-color: #fafbfc;
    border-radius: 3px;
    height: 100%;

    .logo {
      width: 32px;
      height: 32px;
    }

    .url {
      margin-top: 0.5rem;
      margin-bottom: -0.3rem;
    }

    .switchy {
      margin-top: 2.5rem;

      .switch-input {
        &:not(.-checked) {
          background: #c05c67 !important;
          &::after {
            color: #c05c67 !important;
          }
        }
        &.disabled {
          background: #eee !important;
          &::after {
            color: #6c868e !important;
          }
        }
      }
    }
  }
</style>
