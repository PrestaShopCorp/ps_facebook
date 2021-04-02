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
    <b-alert
      variant="success"
      dismissible
      :show="showOnboardSucceeded"
    >
      <p>{{ $t('configuration.messages.success') }}</p>
    </b-alert>

    <b-alert
      variant="danger"
      :show="!!alertSettings.error"
    >
      <p class="clearfix">
        {{ $t(alertSettings.error) }}
        <br>
        <b-button
          class="mt-2 float-right"
          @click="setMerchandAction(alertSettings.error)"
        >
          {{ $t(alertSettings.errorButton) }}
        </b-button>
      </p>
    </b-alert>

    <stepper
      v-if="showSyncCatalogAdvice"
      :category-matching-started="categoryMatchingStarted"
      :product-sync-started="productSyncStarted"
      :ad-campaign-started="adCampaignStarted"
      @onCategoryMatchingClick="onCategoryMatchingClick"
      @onSyncCatalogClick="onSyncCatalogClick"
      @onAdCampaignClick="onAdCampaignClick"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {
  BAlert,
  BButton,
} from 'bootstrap-vue';
import Stepper from './stepper.vue';

export default defineComponent({
  name: 'Messages',
  components: {
    BAlert,
    BButton,
    Stepper,
  },
  props: {
    showOnboardSucceeded: {
      type: Boolean,
      required: false,
      default: false,
    },
    showSyncCatalogAdvice: {
      type: Boolean,
      required: false,
      default: false,
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
    alertSettings: {
      type: Object,
      required: false,
      default: null,
    },
  },
  methods: {
    onCategoryMatchingClick() {
      this.$emit('onCategoryMatchingClick');
    },
    onSyncCatalogClick() {
      this.$emit('onSyncCatalogClick');
    },
    onAdCampaignClick() {
      this.$emit('onAdCampaignClick');
    },
    setMerchandAction(errors) {
      if (errors.length > 0) {
        this.$parent.openPopup();
      } else {
        window.document.location.reload();
      }
    },
  },
});
</script>
