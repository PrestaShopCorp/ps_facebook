<template>
  <ps-banner
    variant="info"
  >
    <div
      class="d-flex align-items-center"
    >
      <div>
        <div
          class="ps_gs-fz-24 font-weight-700 mb-2 prestafont"
        >
          <i
            class="material-icons-round mr-1 pb-1"
          >local_fire_department</i>
          {{ $t('configuration.catalogBanner.title') }}
        </div>
        {{ $t('configuration.catalogBanner.subTitle') }}
      </div>

      <div class="d-md-flex ml-auto text-center">
        <b-button
          class="mx-1 ml-md-0 mr-md-1"
          variant="primary"
          @click="openModal"
        >
          {{ $t('cta.synchronizeCatalog') }}
        </b-button>
      </div>
    </div>

    <ps-modal
      id="ps_facebook_modal_enable_catalog"
      ref="ps_facebook_modal_enable_catalog"
      :title="$t('catalog.summaryPage.productCatalog.modals.startSharing.title')"
      @ok="onClickOnSyncCatalog"
    >
      <h3>
        {{ $t('catalog.summaryPage.productCatalog.modals.startSharing.subTitle') }}
      </h3>
      <p
        v-html="md2html($t('catalog.summaryPage.productCatalog.modals.startSharing.description'))"
      />
      <template slot="modal-cancel">
        {{ $t('cta.cancel') }}
      </template>
      <template slot="modal-ok">
        {{ $t('cta.synchronizeCatalog') }}
      </template>
    </ps-modal>
  </ps-banner>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import showdown from 'showdown';
import PsBanner from '@/components/commons/ps-banner.vue';
import PsModal from '@/components/commons/ps-modal.vue';

export default defineComponent({
  name: 'BannerCatalogSharing',
  components: {
    PsBanner,
    PsModal,
  },
  props: {
    onCatalogPage: {
      type: Boolean,
      required: true,
    },
  },
  methods: {
    openModal(): void {
      this.$bvModal.show(
        this.$refs.ps_facebook_modal_enable_catalog.$refs.modal.id,
      );
    },
    async onClickOnSyncCatalog(): Promise<void> {
      if (!this.onCatalogPage) {
        this.$router.push({
          name: 'Catalog',
        });
      }
      await this.$store.dispatch('catalog/REQUEST_TOGGLE_SYNCHRONIZATION', true);
    },
    md2html: (md: string) => (new showdown.Converter()).makeHtml(md),
  },
});
</script>
