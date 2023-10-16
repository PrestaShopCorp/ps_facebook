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
        :checked="exportOn"
        @click.native.prevent="onExportClicked"
        :disabled="syncToggleRequestStatus === RequestState.PENDING"
        inline
      >
        <span class="small">
          {{ exportOn
            ? $t('catalog.summaryPage.productCatalog.catalogExportActivated')
            : $t('catalog.summaryPage.productCatalog.catalogExportPaused') }}
        </span>
      </b-form-checkbox>
    </b-card-header>

    <b-card-body>
      <verified-products
        :active="exportDoneOnce"
        :loading="false"
        :verifications-stats="validation.prevalidation"
        @triggerScan="triggerProductsScan"
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
          v-if="nextSyncAsFullRequestStatus === RequestState.FAILED"
          variant="warning"
          show
          class="warning"
        >
          {{ $t('catalogSummary.resetExportError') }}
        </b-alert>
        <b-alert
          v-if="nextSyncAsFullRequestStatus === RequestState.SUCCESS"
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
      :title="$t('catalog.summaryPage.productCatalog.modals.deactivation.title')"
      @ok="toggleSyncStatus(false)"
    >
      {{ $t('catalog.summaryPage.productCatalog.modals.deactivation.description') }}
      <template slot="modal-cancel">
        {{ $t('cta.cancel') }}
      </template>
      <template slot="modal-ok">
        {{ $t('cta.modalConfirm') }}
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
import {RequestState} from '@/store/modules/catalog/types';

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
    catalogId: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      RequestState,
    };
  },
  computed: {
    nextSyncAsFullRequestStatus(): RequestState {
      return this.$store.state.catalog.requests.requestNextSyncFull;
    },
    syncToggleRequestStatus(): RequestState {
      return this.$store.state.catalog.requests.syncToggle;
    },
  },
  methods: {
    onExportClicked() {
      const newValue = !this.exportOn;

      if (!newValue) {
        this.$bvModal.show(
          this.$refs.ps_facebook_modal_unsync.$refs.modal.id,
        );
        return; // blocking modal, to confirm deactivation
      }

      this.toggleSyncStatus(newValue);
    },
    toggleSyncStatus(newValue: boolean): void {
      if (newValue) {
        this.$segment.track('[FBK] Share catalog enable', {
          module: 'ps_facebook',
          source: 'toggle',
        });
      } else {
        this.$segment.track('[FBK] Share catalog disable', {
          module: 'ps_facebook',
          source: 'toggle',
        });
      }

      this.$store.dispatch('catalog/REQUEST_TOGGLE_SYNCHRONIZATION', newValue);
    },
    async triggerProductsScan() {
      this.$segment.track('Scan of products triggered', {
        module: 'ps_facebook',
      });

      const numberOfPages = await this.$store.dispatch('catalog/REQUEST_PRODUCT_SCAN');

      this.$segment.track('Scan of products done', {
        module: 'ps_facebook',
        numberOfPages,
      });
    },
    md2html: (md) => (new showdown.Converter()).makeHtml(md),

    async resetSync() {
      await this.$store.dispatch('catalog/REQUEST_NEXT_SYNC_AS_FULL');
      this.$segment.track('[FBK] Full scan requested', {
        module: 'ps_facebook',
      });
    },
  },
  mounted() {
    if (this.validation.prevalidation === null) {
      this.triggerProductsScan();
    }
  },
});
</script>
