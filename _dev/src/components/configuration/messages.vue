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
  <div>
    <b-alert
      variant="success"
      dismissible
      :show="showOnboardSucceeded"
    >
      <p>{{ $t('configuration.messages.success') }}</p>
    </b-alert>

    <b-alert
      variant="warning"
      dismissible
      :show="showSyncCatalogAdvice"
    >
      <p>
        {{ $t('configuration.messages.syncCatalogAdvice') }}
        <br>
        <b-button
          variant="primary"
          class="mt-2"
          @click="onSyncCatalogAdviceClick"
        >
          {{ $t('configuration.messages.syncCatalogButton') }}
        </b-button>
      </p>
    </b-alert>
  </div>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {BAlert, BButton} from 'bootstrap-vue';

export default defineComponent({
  name: 'Messages',
  components: {BAlert, BButton},
  mixins: [],
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
  },
  methods: {
    onSyncCatalogAdviceClick() {
      this.$emit('onSyncCatalogAdviceClick');
    },
  },
});
</script>
