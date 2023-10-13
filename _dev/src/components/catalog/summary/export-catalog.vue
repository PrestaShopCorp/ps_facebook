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
      syncIsActive: this.exportOn as boolean,
      RequestState,
    };
  },
  computed: {
    nextSyncAsFullRequestStatus(): RequestState {
      return this.$store.state.catalog.requests.requestNextSyncFull;
    },
  },
  methods: {
    exportClicked(activate: boolean, confirm: boolean = false) {
      if (!activate && !confirm) {
        this.$bvModal.show(
          this.$refs.ps_facebook_modal_unsync.$refs.modal.id,
        );
        return; // blocking modal, to confirm deactivation
      }

      if (activate) {
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

      this.$store.dispatch('catalog/REQUEST_TOGGLE_SYNCHRONIZATION', activate);
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
