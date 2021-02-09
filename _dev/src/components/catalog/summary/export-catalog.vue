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
        src="@/assets/catalog_export_illustration.png"
        :width="(exportDoneOnce && 48) || 134"
        :height="(exportDoneOnce && 48) || 134"
        alt="background illustration"
      >
    </div>

    <h1 class="title">
      <div
        v-if="exportDoneOnce"
        class="mt-1 ml-3"
      >
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
          @click="exportClicked(!exportOn)"
          data-toggle="modal"
          :data-target="exportOn ? '#ps_facebook_modal_unsync' : null"
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

    <div
      v-if="!exportDoneOnce"
      class="text"
      :class="{ expanded: seeMoreState }"
    >
      {{ $t('catalogSummary.catalogExportIntro') }}
      <br><br>

      <p
        class="app foldable p-2 mb-0"
        v-html="md2html($t('catalogSummary.catalogExportInfo'))"
      />
      <span
        class="see-more"
        @click="seeMore"
      >
        <span>{{ $t('catalogSummary.showMore') }}</span>
        ...
      </span>
      <span
        class="see-less"
        @click="seeLess"
      >{{ $t('catalogSummary.showLess') }}</span>
    </div>

    <div v-else>
      <br>
      <b-container
        fluid
        class="w-100"
      >
        <b-row align-v="stretch">
          <b-col class="counter m-1 p-3">
            <i class="material-icons">sync</i>
            {{ $t('catalogSummary.reportingLastSync') }}
            <span
              v-if="reporting.hasSynced"
              class="big mt-2"
            >
              {{ reporting.syncDate || '--' }}
            </span>
            <span
              v-if="reporting.hasSynced"
              class="text-muted"
            >
              {{ reporting.syncTime || '--' }}
            </span>
            <b-alert
              v-if="!reporting.hasSynced"
              variant="warning"
              show
              class="warning smaller"
            >
              {{ $t('catalogSummary.catalogExportNotice') }}
            </b-alert>
          </b-col>
          <div class="w-100 d-block d-sm-none" />
          <b-col class="counter m-1 p-3">
            <i class="material-icons">store</i>
            {{ $t('catalogSummary.reportingCatalogCount') }}
            <span class="big mt-2">{{ reporting.catalog || '--' }}</span>
          </b-col>
          <div class="w-100 d-block d-md-none" />
          <b-col class="counter m-1 p-3">
            <i class="material-icons">error_outline</i>
            {{ $t('catalogSummary.reportingErrorsCount') }}
            <br>
            <!--b-link
              class="float-right see-details mt-3"
              @click="onDetails"
            >
              {{ $t('catalogSummary.detailsButton') }}
            </b-link-->
            <span class="big mt-2">{{ reporting.errored || '--' }}</span>
          </b-col>
        </b-row>
      </b-container>

      <b-link
        v-if="catalogId"
        class="view-button float-right ml-3 mb-2 mr-2 mt-2"
        @click="onViewCatalog"
        target="_blank"
        :href="viewCatalogUrl"
      >
        <i class="material-icons">launch</i>
        {{ $t('catalogSummary.viewCatalogButton') }}
      </b-link>
      <br clear="both">
    </div>

    <hr class="separator">

    <template>
      <div class="float-right">
        <i
          class="material-icons refresh-icon"
          @click="rescan"
        >loop</i>
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
              <b-link
                class="float-right see-details"
                @click="onDetails"
              >
                {{ $t('catalogSummary.detailsButton') }}
              </b-link>
              <span class="red-number">
                {{ prevalidation.notSyncable }}
              </span>
              /&nbsp;{{ prevalidation.syncable + prevalidation.notSyncable }}
            </div>
          </b-col>
        </b-row>
      </b-container>
    </template>

    <hr class="separator">

    <template v-if="!exportDoneOnce">
      <b-alert
        variant="warning"
        show
        class="warning"
      >
        {{ $t('catalogSummary.catalogExportWarning') }}
      </b-alert>
      <b-button
        class="float-right ml-4"
        :variant="error ? 'danger' : 'primary'"
        @click="exportClicked(true)"
      >
        {{ exportButtonLabel }}
      </b-button>
      <p class="disclaimer text-muted">
        {{ $t('catalogSummary.catalogExportDisclaimer') }}
      </p>
    </template>

    <template v-else>
      <div
        class="text"
        :class="{ expanded: seeMoreState }"
      >
        <p class="app foldable p-2 mb-0">
          <span v-html="md2html($t('catalogSummary.catalogExportInfo'))" />
          <a
            href="javascript:void(0);"
            @click="resetSync"
          >
            {{ $t('catalogSummary.resetExportLink') }}
          </a>
        </p>
        <span
          class="see-more"
          @click="seeMore"
        >
          <span>{{ $t('catalogSummary.showMore') }}</span>
          ...
        </span>
        <span
          class="see-less"
          @click="seeLess"
        >{{ $t('catalogSummary.showLess') }}</span>
      </div>
      <b-alert
        v-if="resetLinkError"
        variant="warning"
        show
        class="warning"
      >
        {{ $t('catalogSummary.resetExportError') }}
      </b-alert>
      <b-alert
        v-if="resetLinkSuccess"
        variant="success"
        show
        class="success"
      >
        {{ $t('catalogSummary.resetExportSuccess') }}
      </b-alert>
      <br v-if="!exportOn">
      <p
        v-if="!exportOn"
        class="mb-0"
      >
        <i class="material-icons pause-icon">pause_circle_filled</i>
        {{ $t('catalogSummary.catalogExportOperationPaused') }}
      </p>
    </template>

    <!-- Confirmation modal for Disabling synchronization -->
    <div
      id="ps_facebook_modal_unsync"
      class="modal"
    >
      <div
        class="modal-dialog"
        role="document"
      >
        <div class="modal-content tw-rounded-none">
          <div class="modal-header">
            <slot name="header">
              <div class="tw-flex tw-items-center">
                <h5 class="modal-title tw-pl-3">
                  {{ $t('catalogSummary.modalDeactivationTitle') }}
                </h5>
              </div>
            </slot>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            {{ $t('catalogSummary.modalDeactivationText') }}
          </div>
          <div class="modal-footer">
            <b-button
              variant="primary"
              target="_blank"
              data-dismiss="modal"
              @click="exportClicked(false, true)"
            >
              {{ $t('integrate.buttons.modalConfirm') }}
            </b-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton, BAlert, BLink} from 'bootstrap-vue';
import showdown from 'showdown';

export default defineComponent({
  name: 'ExportCatalog',
  components: {
    BButton,
    BAlert,
    BLink,
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
    resetProductSyncRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookExportWholeCatalog || null,
    },
    catalogId: {
      type: String,
      required: false,
      default: null,
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
    reporting() {
      const data = this.validation ? this.validation.reporting : {
        lastSyncDate: null,
        catalog: 'N/A',
        errored: 'N/A',
      };

      const syncDate = data.lastSyncDate ? new Date(data.lastSyncDate) : null;

      return {
        hasSynced: !!data.lastSyncDate && syncDate.getFullYear() > 1999,
        syncDate: syncDate?.toLocaleDateString(
          undefined,
          {year: 'numeric', month: 'numeric', day: 'numeric'},
        ),
        syncTime: syncDate?.toLocaleTimeString(
          undefined,
          {hour: '2-digit', minute: '2-digit'},
        ),
        catalog: data.catalog,
        errored: data.errored,
      };
    },
    viewCatalogUrl() {
      return `https://www.facebook.com/products/catalogs/${this.catalogId}/products`;
    },
  },
  data() {
    return {
      error: null,
      seeMoreState: false,
      resetLinkError: null,
      resetLinkSuccess: null,
    };
  },
  methods: {
    exportClicked(activate, confirm = false) {
      if (!activate && !confirm) {
        return; // blocking modal, to confirm deactivation
      }

      if (activate) {
        this.$segment.track('Share catalog enable', {
          module: 'ps_facebook',
        });
      } else {
        this.$segment.track('Share catalog disable', {
          module: 'ps_facebook',
        });
      }

      if (!this.startProductSyncRoute) {
        return;
      }

      fetch(this.startProductSyncRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({
          turn_on: activate,
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
    onDetails() {
      this.$segment.track('See details 2', {
        module: 'ps_facebook',
      });

      this.$parent.goto(this.$parent.PAGES.reportDetails);
    },
    onViewCatalog() {
      this.$segment.track('View catalog', {
        module: 'ps_facebook',
      });
    },
    rescan() {
      window.location.reload();
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),
    seeMore() {
      this.seeMoreState = true;
    },
    seeLess() {
      this.seeMoreState = false;
    },
    resetSync() {
      if (!this.resetProductSyncRoute) {
        return;
      }

      fetch(this.resetProductSyncRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        // return res.json();
      }).then(() => {
        this.resetLinkSuccess = setTimeout(() => {
          this.resetLinkSuccess = null;
        }, 5000);
      }).catch((error) => {
        console.error(error);
        this.resetLinkError = setTimeout(() => {
          this.resetLinkError = null;
        }, 5000);
      });
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

    & .see-details {
      position: relative;
      bottom: -1rem;
    }
  }
  .counter {
    border-radius: 3px;
    border: 1px solid #DCE1E3;
    height: 100%;

    & > i {
      float: left;
      margin-right: 0.6rem;
      margin-bottom: 3.5rem;
      font-size: 1.85rem;
      color: #6C868E;
    }
    &:first-of-type > i {
      color: #24B9D7;
    }
    &:last-of-type > i {
      color: #C05C67;
    }

    & > span.big {
      display: block;
      font-size: 1.5em;
      font-weight: 700;
    }
    &:last-of-type > span {
      color: #C05C67;
    }

    & .warning.smaller {
      font-size: smaller;
      padding-left: 3.4rem !important;
      padding-top: 0.3rem !important;
      padding-bottom: 0.3rem !important;
      margin-bottom: 0 !important;
      margin-top: 0.9rem;
    }
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
  }
  .view-button {
    font-weight: 700;
  }
</style>
