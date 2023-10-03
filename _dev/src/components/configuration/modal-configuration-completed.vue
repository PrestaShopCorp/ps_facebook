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
import PsModal from '@/components/commons/ps-modal.vue';

export default defineComponent({
  name: 'ModalConfigurationCompleted',
  components: {
    PsModal,
  },
  methods: {
    goToProductCatalog() {
      this.$router.push({
        name: 'Catalog',
      });
    },
  },
});
</script>
