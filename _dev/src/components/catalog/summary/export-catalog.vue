<template>
  <b-card
    no-body
  >
    <b-card-header>
      {{ $t('catalog.summaryPage.productCatalog.title') }}

      <b-form-checkbox
        v-if="exportDoneOnce"
        switch
        size="lg"
        class="ml-1 ps_gs-switch"
        v-model="syncIsActive"
        inline
      >
        <span class="small">
          {{ syncIsActive
            ? $t('catalog.summaryPage.productCatalog.catalogExportActivated')
            : $t('catalog.summaryPage.productCatalog.catalogExportPaused') }}
        </span>
      </b-form-checkbox>
    </b-card-header>

    <b-card-body>
      <verified-products
        :active="exportDoneOnce"
        :verifications-stats="validation.prevalidation"
      />
      <submitted-products
        :loading="false"
        :page-is-active="exportDoneOnce"
        :sync-is-active="exportOn"
        :catalog-id="catalogId"
        :validation-summary="validation.reporting"
      />

      <template>
        <p class="app foldable p-2 mb-0">
          <b-button
            variant="link"
            @click="resetSync"
          >
            {{ $t('catalogSummary.resetExportLink') }}
          </b-button>
        </p>

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
      </template>
    </b-card-body>

    <!-- Confirmation modal for Disabling synchronization -->
    <ps-modal
      id="ps_facebook_modal_unsync"
      ref="ps_facebook_modal_unsync"
      :title="$t('catalogSummary.modalDeactivationTitle')"
      @ok="exportClicked(false, true)"
      ok-only
    >
      {{ $t('catalogSummary.modalDeactivationText') }}
      <template slot="modal-ok">
        {{ $t('integrate.buttons.modalConfirm') }}
      </template>
    </ps-modal>
  </b-card>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';
import showdown from 'showdown';
import Spinner from '@/components/spinner/spinner.vue';
import PsModal from '@/components/commons/ps-modal.vue';
import {ProductFeedReport} from '@/store/modules/catalog/state';
import VerifiedProducts from '@/components/catalog/summary/panel/verified-products.vue';
import SubmittedProducts from '@/components/catalog/summary/panel/submitted-products.vue';

export default defineComponent({
  name: 'ExportCatalog',
  components: {
    Spinner,
    PsModal,
    VerifiedProducts,
    SubmittedProducts,
  },
  props: {
    validation: {
      type: Object as PropType<ProductFeedReport>,
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
    runPrevalidationScanRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookRunPrevalidationScanRoute || null,
    },
    catalogId: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      syncIsActive: this.exportOn as boolean,
      error: null,
      resetLinkError: null,
      resetLinkSuccess: null,
      scanProcess: {
        inProgress: false,
        numberOfProductChecked: 0,
        page: 0,
        error: null,
      },
    };
  },
  methods: {
    exportClicked(activate, confirm = false) {
      if (!activate && !confirm) {
        this.$bvModal.show(
          this.$refs.ps_facebook_modal_unsync.$refs.modal.id,
        );
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
    rescan() {
      this.scanProcess = {
        ...this.scanProcess,
        inProgress: true,
        numberOfProductChecked: 0,
        page: 0,
        error: null,
      };

      this.fetchScanPage();
      this.$segment.track('Scan of products triggered', {
        module: 'ps_facebook',
      });
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),

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
        this.resetLinkSuccess = '👌';
      }).catch((error) => {
        console.error(error);
        this.resetLinkError = setTimeout(() => {
          this.resetLinkError = null;
        }, 5000);
      });
    },
    fetchScanPage() {
      return fetch(this.runPrevalidationScanRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({
          page: this.scanProcess.page,
        }),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((json) => {
        if (!json.success) {
          throw new Error(json.message || 'N/A');
        }
        this.scanProcess.inProgress = !json.complete;
        this.scanProcess.numberOfProductChecked = json.progress;
        if (this.scanProcess.inProgress) {
          // Load next page
          this.scanProcess.page += 1;
          this.fetchScanPage();
          return;
        }

        this.$segment.track('Scan of products done', {
          module: 'ps_facebook',
          numberOfPages: this.scanProcess.page,
        });
      }).catch((error) => {
        this.scanProcess.inProgress = false;
        this.scanProcess.error = error;
        console.error(error);
      });
    },
  },
  mounted() {
    if (this.validation.prevalidation === null) {
      this.rescan();
    }
  },
  watch: {
    exportOn(newValue: boolean) {
      this.syncIsActive = newValue;
    },
    syncIsActive(newValue: boolean) {
      this.exportClicked(newValue);
    },
  },
});
</script>
