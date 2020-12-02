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
        :width="(exportDoneOnce && 45) || 112"
        :height="(exportDoneOnce && 48) || 134"
        alt="background illustration"
      >
    </div>

    <h1 class="title">
      <div v-if="exportDoneOnce" class="mt-1 ml-3">
        <span class="d-none d-sm-inline">
          {{
            $t(exportOn
              ? 'catalogSummary.catalogExportActivated'
              : 'catalogSummary.catalogExportPaused'
            )
          }}
        </span>
        <div
          class="switch-input switch-input-lg ml-1"
          :class="exportOn ? '-checked' : null"
          @click="exportClicked"
        >
          <input
            class="switch-input-lg"
            type="checkbox"
            :checked="exportOn"
          >
        </div>
      </div>
      {{ $t('catalogSummary.productCatalogExport') }}
    </h1>
    <div class="text" :class="seeMoreState && 'expanded'">
      {{ $t('catalogSummary.catalogExportIntro') }}
      <br/><br/>
      <p class="app foldable p-2 mb-0"
        v-html="md2html($t('catalogSummary.catalogExportInfo'))"
      />
      <span class="see-more" @click="seeMore"><span>{{ $t('catalogSummary.showMore') }}</span>...</span>
      <span class="see-less" @click="seeLess">{{ $t('catalogSummary.showLess') }}</span>
    </div>

    <hr class="separator">

    <div class="float-right">
      <i class="material-icons refresh-icon" @click="rescan">loop</i>
    </div>
    <h3>
      {{ $t('catalogSummary.preApprovalScanTitle') }}
      <span class="refreshDate text-muted">
        {{ $t('catalogSummary.preApprovalScanRefreshDate', [(new Date()).toLocaleTimeString()]) }}
      </span>
    </h3>
    <p>{{ $t('catalogSummary.preApprovalScanIntro') }}</p>

    <b-container fluid>
      <b-row align-v="stretch">
        <b-col
          lg="6"
          md="6"
          sm="12"
          class="pb-3 px-2"
        >
          <div class="app pt-1 pb-3 px-2">
            <div class="text-uppercase text-muted small-text pb-1">
              {{ $t('catalogSummary.preApprovalScanReadyToSync') }}
            </div>
            <span class="green-number">
              {{ prevalidation.syncable }}
            </span>
            /&nbsp;{{ prevalidation.syncable + prevalidation.notSyncable }}
          </div>
        </b-col>
        <div class="w-100 d-block d-sm-none" />
        <div class="w-100 d-none d-sm-block d-md-none" />
        <b-col
          lg="6"
          md="6"
          sm="12"
          class="pb-3 px-2"
        >
          <div class="app pt-1 pb-3 px-2">
            <div class="text-uppercase text-muted small-text pb-1">
              {{ $t('catalogSummary.preApprovalScanNonSyncable') }}
            </div>
            <div class="float-right see-details">
              More details soon
            </div>
            <span class="red-number">
              {{ prevalidation.notSyncable }}
            </span>
            /&nbsp;{{ prevalidation.syncable + prevalidation.notSyncable }}
          </div>
        </b-col>
      </b-row>
    </b-container>

    <hr class="separator">

    <template v-if="!exportOn">
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
      <p v-if="!exportDoneOnce" class="disclaimer text-muted">
        {{ $t('catalogSummary.catalogExportDisclaimer') }}
      </p>
      <template v-else>
        <b-link
          class="view-button float-right ml-3"
          @click="viewCatalogClicked"
        >
          <i class="material-icons">launch</i>
          {{ $t('catalogSummary.viewCatalogButton') }}
        </b-link>
        <p class="mb-0">
          <i class="material-icons pause-icon">pause_circle_filled</i>
          {{ $t('catalogSummary.catalogExportOperationPaused') }}
        </p>
      </template>
    </template>

    <template v-else>
      <b-alert variant="info" show class="warning">
        {{ $t('catalogSummary.catalogExportNotice') }}
      </b-alert>
      <br>
      <b-link
        class="view-button float-right ml-3"
        @click="viewCatalogClicked"
      >
        <i class="material-icons">launch</i>
        {{ $t('catalogSummary.viewCatalogButton') }}
      </b-link>
    </template>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton, BAlert} from 'bootstrap-vue';
import showdown from 'showdown';
import illustration from '../../../assets/catalog_export_illustration.png';

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
    prevalidation() {
      return this.validation ? this.validation.prevalidation : {syncable: 0, notSyncable: 0};
    },
  },
  data() {
    return {
      illustration,
      error: null,
      seeMoreState: false,
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
    rescan() {
      window.location.reload();
    },
    viewCatalogClicked() {
      // TODO !1: need URL, target blank !
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
    seeMore() {
      this.seeMoreState = true;
    },
    seeLess() {
      this.seeMoreState = false;
    },
  },
});
</script>

<style lang="scss" scoped>
  .illustration {
    margin-bottom: -10px;

    & > img {
      margin-bottom: 6rem;
    }
  }
  .title {
    font-weight: 600;

    & > div {
      float: right;
      font-size: 0.6em;
      font-weight: 300;
    }
  }
  .text {
    display: flow-root;
    margin-bottom: 0;
    position: relative;

    & > div {
      font-size: small;
      padding-left: 3.2rem !important;
      padding-top: 0.6rem !important;
      padding-bottom: 0;
    }

    & .foldable {
      font-size: small;
      display: block;
      height: 8.5em !important;
      overflow-y: hidden;
    }

    & > span {
      position: absolute;
      right: 0;
      bottom: 0;
      padding-top: 0.2rem;
      padding-bottom: 0.3rem;
      padding-right: 0.9rem;
      padding-left: 2.2rem;
      font-size: small;

      &.see-more {
        width: 100%;
        background: #fafbfc;
        border-radius: 3px;
        cursor: s-resize;

        & > span {
          float: right;
          font-weight: bold;
        }
      }
      &.see-less {
        visibility: hidden;
        cursor: n-resize;
        font-weight: bold;
      }
    }

    &.expanded {
      & .foldable {
        height: 100% !important;
      }
      & .see-more {
        visibility: hidden;
      }
      & .see-less {
        visibility: visible;
      }
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
  .pause-icon {
    font-size: 1.5em;
    color: #6C868E;
  }
  .refresh-icon {
    font-size: 1.8em;
    color: #6C868E;
    cursor: pointer;
  }
  .app {
    background-color: #fafbfc;
    border-radius: 3px;
    height: 100%;
  }
  .green-number {
    font-size: 1.3em;
    font-weight: 700;
    color: #70B580;
  }
  .red-number {
    font-size: 1.3em;
    font-weight: 700;
    color: #C05C67;
  }
  .see-details {
    font-size: small;
    font-style: italic;
    position: relative;
    bottom: -1rem;
  }
  .view-button {
    font-weight: 700;
    position: absolute;
    bottom: 0.8rem;
    right: 1rem;
  }
</style>
