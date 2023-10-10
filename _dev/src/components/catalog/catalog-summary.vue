<template>
  <spinner v-if="loading" />
  <div
    v-else
    id="catalogSummary"
  >
    <b-card class="card m-3 p-3">
      <export-catalog
        :validation="GET_SYNCHRONIZATION_SUMMARY"
        :export-done-once="GET_CATALOG_PAGE_ENABLED"
        :export-on="GET_SYNCHRONIZATION_ACTIVE"
        :catalog-id="GET_CATALOG_ID"
      />
    </b-card>

    <b-card
      class="card m-3 p-3"
    >
      <categories-matched
        v-if="GET_CATEGORY_MATCHING_SUMMARY && GET_CATEGORY_MATCHING_SUMMARY.matchingDone"
        :matching-progress="GET_CATEGORY_MATCHING_SUMMARY.matchingProgress"
      />
      <match-categories
        v-else
        :is-primary-action="GET_SYNCHRONIZATION_ACTIVE"
      />
    </b-card>

    <survey />
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {BCard} from 'bootstrap-vue';
import {mapGetters} from 'vuex';
import Spinner from '@/components/spinner/spinner.vue';

import CatalogTabPages from '@/components/catalog/pages';
import ExportCatalog from '@/components/catalog/summary/export-catalog.vue';
import MatchCategories from '@/components/catalog/summary/match-categories.vue';
import CategoriesMatched from '@/components/catalog/summary/categories-matched.vue';
import Survey from '@/components/survey/survey.vue';
import GettersTypesCatalog from '@/store/modules/catalog/getters-types';
import GettersTypesOnboarding from '@/store/modules/onboarding/getters-types';

export default defineComponent({
  name: 'CatalogSummary',
  components: {
    Spinner,
    BCard,
    ExportCatalog,
    MatchCategories,
    CategoriesMatched,
    Survey,
  },
  data() {
    return {
      CatalogTabPages,
      loading: false,
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
    goto(page) {
      return this.$parent && this.$parent.goto(page);
    },
    back() {
      return this.$parent && this.$parent.back();
    },
  },
  watch: {
    GET_SYNCHRONIZATION_ACTIVE(newValue: boolean): void {
      this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
        psx_ps_product_catalog_exported_click: newValue,
      });
    },
  },
});
</script>
