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
    <div class="illustration float-left mr-3 d-none d-md-block">
      <img
        :src="illustration"
        width="112"
        height="134"
        alt="background illustration"
      >
    </div>

    <h3 class="title">{{ $t('catalogSummary.productCatalogExport') }}</h3>
    <b-button
      class="float-right ml-4"
      :variant="isPrimaryAction ? 'primary' : 'outline-secondary'"
      @click="exportClicked"
    >
      {{ $t('catalogSummary.exportCatalogButton') }}
    </b-button>
    <p class="text">
      <b-alert variant="warning" show>
        {{ $t('catalogSummary.catalogExportWarning') }}
      </b-alert>
      {{ $t('catalogSummary.catalogExportIntro') }}
    </p>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton, BAlert} from 'bootstrap-vue';

import illustration from '../../../assets/catalog_export_illustration.png';

export default defineComponent({
  name: 'ExportCatalog',
  components: {
    BButton,
    BAlert,
  },
  props: {
    isPrimaryAction: {
      type: Boolean,
      required: false,
      default: false,
    },
    startProductSyncRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookStartProductSyncRoute || null,
    },
  },
  computed: {
  },
  data() {
    return {
      illustration,
    };
  },
  methods: {
    exportClicked() {
      if (!this.startProductSyncRoute) {
        return;
      }

      // TODO !0: call PHP here, with a fetch POST to this.startProductSyncRoute.
      //  The result should be 200, and then: this.$parent.fetchData()
      alert('Not yet implemented');
    },
  },
});
</script>

<style lang="scss" scoped>
  .illustration {
    margin-bottom: -10px;
  }
  .title {
    font-weight: 600;
  }
  .text {
    display: flow-root;

    & > div {
      padding-left: 3.8rem !important;
    }
  }
</style>
