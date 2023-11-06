<template>
  <div>
    <i18n
      path="catalog.summaryPage.productCatalog.fullScanRequest.resetExportLink"
      tag="div"
      class="py-2 mb-0"
    >
      <b-link
        @click="resetSync"
      >
        {{ $t('cta.clickHere') }}
      </b-link>
    </i18n>

    <b-alert
      v-if="nextSyncAsFullRequestStatus === RequestState.FAILED"
      variant="warning"
      show
      class="warning"
    >
      {{ $t('catalog.summaryPage.productCatalog.fullScanRequest.resetExportError') }}
    </b-alert>
    <b-alert
      v-if="nextSyncAsFullRequestStatus === RequestState.SUCCESS"
      variant="success"
      show
      class="success"
    >
      {{ $t('catalog.summaryPage.productCatalog.fullScanRequest.resetExportSuccess') }}
    </b-alert>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {RequestState} from '@/store/types';

export default defineComponent({
  name: 'FullSyncRequest',
  data() {
    return {
      RequestState,
    };
  },
  computed: {
    nextSyncAsFullRequestStatus(): RequestState {
      return this.$store.state.catalog.requests.requestNextSyncFull;
    },
  },
  methods: {
    async resetSync() {
      await this.$store.dispatch('catalog/REQUEST_NEXT_SYNC_AS_FULL');
      this.$segment.track('[FBK] Full scan requested', {
        module: 'ps_facebook',
      });
    },
  },
});
</script>
