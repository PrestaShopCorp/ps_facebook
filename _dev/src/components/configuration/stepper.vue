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
  <b-card class="card">
    <div class="illustration float-left d-none d-md-block">
      <img
        src="@/assets/illustration.png"
        width="271"
        height="200"
        alt="background illustration"
      >
    </div>

    <div class="title px-3">
      <h3>{{ $t('configuration.messages.stepperTitle') }}</h3>

      <div class="mb-2">
        <stepper-icon :state="psAccountsOnboarded ? 'DONE' : 'AVAILABLE'" />
        {{ $t('configuration.messages.stepPsAccount') }}
      </div>

      <div class="mb-2">
        <stepper-icon
          :state="psFacebookOnboarded ? 'DONE' : (psFbOnboardAvailable ? 'AVAILABLE' : 'DISABLED')"
        />
        <span :class="!psFbOnboardAvailable && 'text-muted'">
          {{ $t('configuration.messages.stepPsFacebook') }}
        </span>
      </div>

      <div
        class="mb-2"
        hidden
      >
        <stepper-icon
          :state="categoryMatchingStarted ? 'DONE' : (catMatchClickable ? 'AVAILABLE' : 'DISABLED')"
        />
        <a
          v-if="catMatchClickable"
          @click="onCategoryMatchingClick"
          href="javascript:void(0)"
          :class="!categoryMatchingStarted && 'bold'"
        >
          {{ $t('configuration.messages.stepCategoryMatching') }}
        </a>
        <span
          v-else
          class="text-muted"
        >
          {{ $t('configuration.messages.stepCategoryMatching') }}
        </span>
        <span class="italic text-muted">
          {{ $t('configuration.messages.stepCategoryMatchingOptional') }}
        </span>
      </div>

      <div class="mb-2">
        <stepper-icon
          :state="productSyncStarted ? 'DONE' : (productSyncClickable ? 'AVAILABLE' : 'DISABLED')"
        />
        <a
          v-if="productSyncClickable"
          @click="onSyncCatalogClick"
          href="javascript:void(0)"
          :class="!productSyncStarted && 'bold'"
        >
          {{ $t('configuration.messages.stepProductSync') }}
        </a>
        <span
          v-else
          class="text-muted"
        >
          {{ $t('configuration.messages.stepProductSync') }}
        </span>
      </div>

      <div class="mb-2">
        <stepper-icon
          :state="adCampaignStarted ? 'DONE' : (adCampaignClickable ? 'AVAILABLE' : 'DISABLED')"
        />
        <a
          v-if="adCampaignClickable"
          @click="onAdCampaignClick"
          href="javascript:void(0)"
          :class="!adCampaignStarted && 'bold'"
        >
          {{ $t('configuration.messages.stepAdCampaign') }}
        </a>
        <span
          v-else
          class="text-muted"
        >
          {{ $t('configuration.messages.stepAdCampaign') }}
        </span>
      </div>
    </div>
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {BCard} from 'bootstrap-vue';
import StepperIcon from './stepper-icon.vue';

export default defineComponent({
  name: 'Stepper',
  components: {
    BCard,
    StepperIcon,
  },
  props: {
    psAccountsOnboarded: { // TODO !1: use when we want stepper event if onboardings are not done
      type: Boolean,
      required: false,
      default: true,
    },
    psFacebookOnboarded: { // TODO !1: use when we want stepper event if onboardings are not done
      type: Boolean,
      required: false,
      default: true,
    },
    categoryMatchingStarted: {
      type: Boolean,
      required: false,
      default: false,
    },
    productSyncStarted: {
      type: Boolean,
      required: false,
      default: false,
    },
    adCampaignStarted: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  computed: {
    psFbOnboardAvailable() {
      return this.psAccountsOnboarded;
    },
    catMatchClickable() {
      return this.psAccountsOnboarded && this.psFacebookOnboarded;
    },
    productSyncClickable() {
      return this.psAccountsOnboarded && this.psFacebookOnboarded;
    },
    adCampaignClickable() {
      return this.psAccountsOnboarded && this.psFacebookOnboarded;
    },
  },
  methods: {
    onCategoryMatchingClick() {
      this.$emit('onCategoryMatchingClick');
      this.$segment.track('Click on map categories', {
        module: 'ps_facebook',
      });
    },
    onSyncCatalogClick() {
      this.$emit('onSyncCatalogClick');
      this.$segment.track('Click on export product catalog', {
        module: 'ps_facebook',
      });
    },
    onAdCampaignClick() {
      this.$emit('onAdCampaignClick');
      this.$segment.track('Click on dynamics ads', {
        module: 'ps_facebook',
      });
    },
  },
});
</script>

<style lang="scss" scoped>
  .card {
    border: none;
    border-radius: 3px;
    overflow: hidden;
    box-shadow: 0 5px 10px 0 rgba(0,0,0,0.1);

    & .title {
      display: flow-root;

      & > h3 {
        font-weight: 600;
        line-height: 1.5em;
      }
    }

    & .bold {
      font-weight: 600;
    }

    & .italic {
      font-style: italic;
    }
  }

  .opacity-50 {
    opacity: 0.5;
  }
</style>
