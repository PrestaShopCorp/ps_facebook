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

    <h1 class="title">
      {{ $t('catalogSummary.productCatalogExport') }}
    </h1>
    <p class="text">
      {{ $t('catalogSummary.catalogExportIntro') }}
      <br><br>
      <b-alert
        variant="info"
        show
        v-html="md2html($t('catalogSummary.catalogExportInfo'))"
      />
    </p>

    <hr class="separator">

    <div class="float-right">
      ### refresh button
    </div>
    <h3>
      {{ $t('catalogSummary.preApprovalScanTitle') }}
      <span class="refreshDate text-muted">
        {{ $t('catalogSummary.preApprovalScanRefreshDate', [(new Date()).toLocaleTimeString()]) }}
      </span>
    </h3>
    <p>{{ $t('catalogSummary.preApprovalScanIntro') }}</p>
    ### 2 cards - {{ validation }}

    <hr class="separator">

    <b-alert variant="warning" show class="warning">
      {{ $t('catalogSummary.catalogExportWarning') }}
    </b-alert>
    <b-button
      v-if="!exportDoneOnce"
      class="float-right ml-4"
      :variant="error ? 'danger' : 'primary'"
      @click="exportClicked"
    >
      {{ exportButtonLabel }}
    </b-button>
    <p class="disclaimer text-muted">{{ $t('catalogSummary.catalogExportDisclaimer') }}</p>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton, BAlert} from 'bootstrap-vue';

import illustration from '../../../assets/catalog_export_illustration.png';
import showdown from "showdown";

export default defineComponent({
  name: 'ExportCatalog',
  components: {
    BButton,
    BAlert,
  },
  props: {
    validation: {
      type: Object,
      required: true,
    },
    exportDoneOnce: {
      type: Boolean,
      required: true,
    },
    exportOn: {
      type: Boolean,
      required: true,
    },
    startProductSyncRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookStartProductSyncRoute || null,
    },
  },
  computed: {
    exportButtonLabel() {
      return this.error
        ? this.$t('catalogSummary.exportCatalogButtonErrored')
        : this.$t('catalogSummary.exportCatalogButton');
    },
  },
  data() {
    return {
      illustration,
      error: null,
    };
  },
  methods: {
    exportClicked() {
      if (!this.startProductSyncRoute) {
        return;
      }

      fetch(this.startProductSyncRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({
          turnOn: this.exportDoneOnce ? !this.exportOn : true,
        }),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (!res.success) {
          throw new Error(res.statusText || res.status);
        }
        this.$parent.fetchData();
      }).catch((error) => {
        console.error(error);
        this.error = setTimeout(() => {
          this.error = null;
        }, 5000);
      });
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
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
    margin-bottom: 0;

    & > div {
      font-size: small;
      padding-left: 3.2rem !important;
      padding-top: 0.6rem !important;
      padding-bottom: 0;
    }
  }
  .separator {
    margin-top: 0.7rem;
    margin-bottom: 1.4rem;
  }
  .refreshDate {
    padding-left: 1rem;
    font-size: 14px;
    font-style: italic;
    font-weight: 300;
  }
  .warning {
    font-size: small;
    padding-left: 3.8rem !important;
    padding-top: 0.6rem !important;
  }
  .disclaimer {
    font-size: smaller;
    font-style: italic;
    margin-bottom: 0;
  }
</style>
