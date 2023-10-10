<template>
  <ps-banner
    variant="info"
  >
    <div
      class="d-flex align-items-center"
    >
      <div>
        <div
          class="ps_gs-fz-24 font-weight-700 mb-2"
        >
          <i
            class="material-icons mr-1 pb-1"
          >local_fire_department</i>
          {{ $t('configuration.catalogBanner.title') }}
        </div>
        {{ onCatalogPage
          ? $t('configuration.catalogBanner.subTitleOnCatalogPage')
          : $t('configuration.catalogBanner.subTitle') }}
      </div>

      <div class="d-md-flex ml-auto text-center">
        <b-button
          class="mx-1 ml-md-0 mr-md-1"
          variant="primary"
          @click="onClickOnSyncCatalog"
        >
          {{ $t('cta.synchronizeCatalog') }}
        </b-button>
      </div>
    </div>
  </ps-banner>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import PsBanner from '@/components/commons/ps-banner.vue';

export default defineComponent({
  name: 'BannerCatalogSharing',
  components: {
    PsBanner,
  },
  props: {
    onCatalogPage: {
      type: Boolean,
      required: true,
    },
  },
  methods: {
    onClickOnSyncCatalog(): void {
      if (this.onCatalogPage) {
        this.$emit('enableSyncAndCatalogPage');
        return;
      }
      this.$router.push({
        name: 'Catalog',
      });
    },
  },
});
</script>
