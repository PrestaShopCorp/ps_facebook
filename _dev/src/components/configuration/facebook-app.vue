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
  <div class="app pb-2 px-2">
    <div class="d-flex">
      <img
        v-if="!!logo"
        :src="logo"
        alt="app logo"
        class="logo mr-3 my-1"
      >
      <div>
        <div class="font-weight-500 d-flex ps_gs-fz-16 mb-2">
          {{ appType }}
          <tooltip 
            v-if="!!tooltip"
            :text="tooltip"
          />
        </div>
        <div class="font-weight-500 ps_gs-fz-14 text-truncate">
          {{ appName }}

          <span
            v-if="!!url"
            class="url"
          >
            &nbsp;/&nbsp;
            <b-link
              :href="url"
              target="_blank"
              @click="onStats"
            >
              {{ $t('configuration.app.viewStats') }}
            </b-link>
          </span>
        </div>

        <div v-if="displayWarning">
          <warning :warning-text="$t('configuration.app.informationCannotBeDisplayedWarning')" />
        </div>
        <div v-else>
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
            {{ $tc('configuration.app.nbLikes', likes, [likes]) }}
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
          <div
            v-if="activationSwitch"
            class="small"
          >
            {{ $t('configuration.app.status') }}
            <b-form-checkbox
              switch
              size="lg"
              class="ml-1 ps_gs-switch"
              v-model="switchActivated"
              :disabled="frozenSwitch"
              inline
            >
              <span class="small">
                {{ statusText }}
              </span>
            </b-form-checkbox>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {BFormCheckbox, BLink} from 'bootstrap-vue';
import Tooltip from '@/components/help/tooltip.vue';
import Warning from '@/components/warning/warning.vue';

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
      switchActivated: this.activationSwitch as boolean,
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

    },
    onStats() {
      this.$segment.track('Click on view stat CTA', {
        module: 'ps_facebook',
      });
    },
  },
  watch: {
    activationSwitch(newValue: boolean) {
      this.switchActivated = newValue;
    },
    switchActivated(newValue: boolean) {
      this.$emit('onActivation', newValue);
      this.$segment.track('Click on pixel switch CTA', {
        module: 'ps_facebook',
      });
      this.$segment.track(`Feature Pixel ${newValue ? 'enabled' : 'disabled'}`, {
        module: 'ps_facebook',
      });
    },
  },
});
</script>
