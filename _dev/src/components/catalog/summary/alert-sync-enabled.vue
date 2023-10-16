<template>
  <b-alert
    show
    variant="success"
    dismissible
  >
    <div
      class="d-flex flex-column flex-md-row justify-content-between"
    >
      <p class="mb-0">
        <strong>
          {{ $t('catalog.summaryPage.productCatalog.alertSyncIsEnabled.title') }}
        </strong>
        <br>
        <span class="ps_gs-fz-12">
          {{ $t('catalog.summaryPage.productCatalog.alertSyncIsEnabled.explanation') }}
        </span>
      </p>
      <div class="d-md-flex flex-grow-1 text-center align-items-end mt-2">
        <b-link
          class="text-nowrap ml-auto"
          @click="$router.push({
            name: 'help',
          })"
        >
          {{ $t('cta.learnMore') }}
        </b-link>
      </div>
    </div>
  </b-alert>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'AlertSyncEnabled',
});
</script>
