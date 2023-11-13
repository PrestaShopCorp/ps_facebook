<template>
  <b-card
    no-body
    class="catalogSummary__card"
  >
    <b-card-header>
      {{ $t('catalog.summaryPage.productCatalog.title') }}
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
        @toggleSync="onExportClicked"
      />
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
        {{ $t('cta.pauseSyncOfCatalog') }}
      </template>
    </ps-modal>
  </b-card>
</template>

<script lang="ts">
import {PropType, defineComponent} from 'vue';
import showdown from 'showdown';
import PsModal from '@/components/commons/ps-modal.vue';
import {ProductFeedReport} from '@/store/modules/catalog/state';
import VerifiedProducts from '@/components/catalog/summary/panel/verified-products.vue';
import SubmittedProducts from '@/components/catalog/summary/panel/submitted-products.vue';
import {RequestState} from '@/store/types';

export default defineComponent({
  name: 'ExportCatalog',
  components: {
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
  },
  mounted() {
    if (this.validation.prevalidation.lastScanDate === null) {
      this.triggerProductsScan();
    }
  },
});
</script>
