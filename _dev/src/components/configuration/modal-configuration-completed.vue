<template>
  <ps-modal
    :title="$t('configuration.modal.configCompleted.title')"
    @ok="goToProductCatalog"
    visible
  >
    <p
      class="font-weight-700 ps_gs-fz-18"
    >
      {{ $t('configuration.modal.configCompleted.nextStep') }}
    </p>
    <p>
      {{ $t('configuration.modal.configCompleted.remainingSteps') }}
    </p>
    <p
      v-html="md2html($t('catalog.summaryPage.productCatalog.modals.startSharing.description'))"
    />
    <template slot="modal-ok">
      {{ $t('cta.synchronizeCatalog') }}
    </template>
    <template slot="modal-cancel">
      {{ $t('cta.cancel') }}
    </template>
  </ps-modal>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Showdown from 'showdown';
import PsModal from '@/components/commons/ps-modal.vue';

export default defineComponent({
  name: 'ModalConfigurationCompleted',
  components: {
    PsModal,
  },
  methods: {
    goToProductCatalog(): void {
      this.$router.push({
        name: 'Catalog',
      });
      this.$store.dispatch('catalog/REQUEST_TOGGLE_SYNCHRONIZATION', true);
    },
    md2html: (md: string) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>
