<template>
  <b-alert
    show
    variant="warning"
    dismissible
  >
    <div
      class="d-flex flex-column flex-md-row justify-content-between"
    >
      <p class="mb-0">
        <strong>
          {{ $t('catalog.summaryPage.productCatalog.alertSyncIsDisabled.title') }}
        </strong>
        <br>
        <span class="ps_gs-fz-12">
          {{ $t('catalog.summaryPage.productCatalog.alertSyncIsDisabled.explanation') }}
        </span>
      </p>
      <div class="d-md-flex flex-grow-1 text-center align-items-center mt-2">
        <b-button
          size="sm"
          class="mx-1 mt-3 mt-md-0 mr-md-1 text-nowrap"
          variant="outline-primary"
          @click="enableSync"
        >
          {{ $t('cta.synchronizeCatalog') }}
        </b-button>
      </div>
    </div>
  </b-alert>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'AlertSyncDisabled',
  methods: {
    enableSync() {
      this.$segment.track('[FBK] Share catalog enable', {
        module: 'ps_facebook',
        source: 'alert',
      });
      this.$store.dispatch('catalog/REQUEST_TOGGLE_SYNCHRONIZATION', true);
    },
  },
});
</script>
