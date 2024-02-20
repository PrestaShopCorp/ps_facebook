<template>
  <loading-page-spinner v-if="loading" />

  <b-card
    class="card m-3"
    v-else
    id="catalogCategoryMatchingEdit"
  >
    <div>
      <b-button
        class="float-left mr-3"
        variant="outline-secondary"
        @click="$router.push({
          name: 'Catalog',
        })"
      >
        <i class="material-icons material-icons-round">keyboard_backspace</i>
        {{ $t('catalogSummary.backButton') }}
      </b-button>
      <div class="counter float-right ml-5">
        <h3 :class="matchingDone">
          {{ matchingProgress.matched }}
          / {{ matchingProgress.total }}
          <br>
          <span>{{ $t('categoryMatching.counterSubTitle') }}</span>
        </h3>
      </div>
      <h1>{{ $t('catalogSummary.categoryMatching') }}</h1>
    </div>


    <p
      class="py-3"
      v-html="md2html($t('categoryMatching.intro'))"
    />

    <b-button
      class="float-right ml-3"
      variant="primary"
      @click="goToCategoryMatchingEditPage"
    >
      {{ $t('categoryMatching.edit') }}
    </b-button>

    <EditTable
      v-if="categories.length > 0"
      :initial-categories="categories"
    />
  </b-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {mapGetters} from 'vuex';
import Showdown from 'showdown';
import EditTable from '@/components/category-matching/editTable.vue';
import MixinMatching from '@/components/category-matching/matching';
import LoadingPageSpinner from '@/components/spinner/loading-page-spinner.vue';
import GettersTypesCatalog from '@/store/modules/catalog/getters-types';
import CatalogTabPages from './pages';

export default defineComponent({
  name: 'CatalogMatchingView',
  components: {
    LoadingPageSpinner,
    EditTable,
  },
  mixins: [
    MixinMatching,
  ],
  computed: {
    ...mapGetters('catalog', [
      GettersTypesCatalog.GET_CATEGORY_MATCHING_SUMMARY,
    ]),
    matchingDone() {
      if (this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress
        && this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress.matched
        === this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress.total
      ) {
        return 'matching-finished';
      }
      return '';
    },
    matchingProgress() {
      return {
        matched: this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress?.matched ?? '--',
        total: this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress?.total ?? '--',
      };
    },
  },
  data() {
    return {
      loading: true as boolean,
      categories: [] as unknown[],
    };
  },
  async mounted() {
    await Promise.allSettled([
      this.fetchCategoryMatchingCounters(),
      this.fetchCategories(0, 1),
    ]);

    this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
      psx_ps_category_mapping_tool_clicked: true,
    });
  },
  methods: {
    async fetchCategoryMatchingCounters(): Promise<void> {
      this.$store.dispatch('catalog/REQUEST_CATEGORY_MAPPING_STATS');
    },

    async fetchCategories(idCategory: number, page: number) {
      const mainCategoryId = idCategory || this.$store.state.context.appContext.defaultCategory.id_category;
      this.loading = true;

      try {
        const categories = await this.$store.dispatch('catalog/REQUEST_CATEGORY_MAPPING_LIST', {
          idCategory: mainCategoryId, page
        });
        this.categories = this.setValuesFromRequest(categories);
      } catch (error) {
        console.error(error);
      }
      this.loading = false;
    },
    goToCategoryMatchingEditPage() {
      this.$router.push({
        name: CatalogTabPages.categoryMatchingEdit,
      });
    },
    md2html: (md) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>
