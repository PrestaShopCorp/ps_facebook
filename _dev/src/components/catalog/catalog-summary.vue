<template>
  <spinner v-if="loading" />
  <div
    v-else
    id="catalogSummary"
  >
    <banner-catalog-sharing
      v-if="!GET_CATALOG_PAGE_ENABLED"
      :on-catalog-page="true"
      class="m-3"
    />

    <alert-sync-disabled
      v-if="GET_CATALOG_PAGE_ENABLED && !GET_SYNCHRONIZATION_ACTIVE"
      class="m-3"
    />

    <alert-sync-enabled
      v-if="showSyncEnabledAlert"
      class="m-3"
    />

    <export-catalog
      :validation="GET_SYNCHRONIZATION_SUMMARY"
      :export-done-once="GET_CATALOG_PAGE_ENABLED"
      :export-on="GET_SYNCHRONIZATION_ACTIVE"
      :catalog-id="GET_CATALOG_ID"
      class="m-3"
    />

    <category-matching
      :matching-progress="GET_CATEGORY_MATCHING_SUMMARY"
      :active="GET_CATALOG_PAGE_ENABLED"
      class="m-3"
    />

    <survey />
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {mapGetters} from 'vuex';
import Spinner from '@/components/spinner/spinner.vue';
import CatalogTabPages from '@/components/catalog/pages';
import ExportCatalog from '@/components/catalog/summary/export-catalog.vue';
import CategoryMatching from '@/components/catalog/summary/category-matching.vue';
import Survey from '@/components/survey/survey.vue';
import GettersTypesCatalog from '@/store/modules/catalog/getters-types';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';
import BannerCatalogSharing from '@/components/catalog/summary/banner-catalog-sharing.vue';
import AlertSyncDisabled from './summary/alert-sync-disabled.vue';
import AlertSyncEnabled from './summary/alert-sync-enabled.vue';

export default defineComponent({
  name: 'CatalogSummary',
  components: {
    AlertSyncDisabled,
    AlertSyncEnabled,
    BannerCatalogSharing,
    CategoryMatching,
    ExportCatalog,
    Spinner,
    Survey,
},
  data() {
    return {
      CatalogTabPages,
      loading: false,
      showSyncEnabledAlert: false as boolean,
    };
  },
  created() {
    this.fetchData();
  },
  computed: {
    ...mapGetters('catalog', [
      GettersTypesCatalog.GET_CATALOG_PAGE_ENABLED,
      GettersTypesCatalog.GET_SYNCHRONIZATION_ACTIVE,
      GettersTypesCatalog.GET_SYNCHRONIZATION_SUMMARY,
      GettersTypesCatalog.GET_CATEGORY_MATCHING_SUMMARY,
    ]),
    ...mapGetters('onboarding', [
      GettersTypesOnboarding.GET_CATALOG_ID,
    ]),
  },
  methods: {
    async fetchData(): Promise<void> {
      this.loading = true;

      try {
        await this.$store.dispatch('catalog/REQUEST_SYNCHRONIZATION_STATS');
      } catch {
        // Do nothing... yet?
      }
      this.loading = false;
    },
  },
  watch: {
    GET_SYNCHRONIZATION_ACTIVE(newValue: boolean): void {
      this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
        psx_ps_product_catalog_exported_click: newValue,
      });

      if (!this.loading) {
        this.showSyncEnabledAlert = newValue;
      }
    },
  },
});
</script>
