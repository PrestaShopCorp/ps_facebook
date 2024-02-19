<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <loading-page-spinner v-if="loading" />

  <b-card
    class="card m-3"
    v-else
    id="catalogCategoryMatchingEdit"
  >
    <!-- Large screen -->
    <div class="d-none d-md-block">
      <b-button
        class="float-left mr-3"
        variant="outline-secondary"
        @click="$router.push({
          name: 'Catalog',
        })"
      >
        <i class="material-icons-round">keyboard_backspace</i>
        {{ $t('catalogSummary.backButton') }}
      </b-button>
      <div class="counter float-right ml-5">
        <h3 :class="matchingDone">
          {{ matchingProgress.matched }}
          / {{ matchingProgress.total }}          <br>
          <span>{{ $t('categoryMatching.counterSubTitle') }}</span>
        </h3>
      </div>
      <h1>{{ $t('catalogSummary.categoryMatching') }}</h1>
    </div>

    <!-- Small screen -->
    <div class="d-block d-md-none">
      <b-button
        class="w-auto mb-3"
        variant="outline-secondary"
        @click="$router.push({
          name: 'Catalog',
        })"
      >
        <i class="material-icons-round">keyboard_backspace</i>
        {{ $t('catalogSummary.backButton') }}
      </b-button>
      <h1>{{ $t('catalogSummary.categoryMatching') }}</h1>
      <div class="counter">
        <h3 :class="matchingDone">
          {{ matchingProgress.matched }}
          / {{ matchingProgress.total }}
          <br>
          <span>{{ $t('categoryMatching.counterSubTitle') }}</span>
        </h3>
      </div>
    </div>

    <p
      class="py-3"
      v-html="md2html($t('categoryMatching.intro'))"
    />

    <b-button
      class="float-right ml-3"
      variant="primary"
      @click="$parent.goto($parent.CatalogTabPages.categoryMatchingView)"
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

export default defineComponent({
  name: 'CatalogMatchingEdit',
  components: {
    LoadingPageSpinner,
    EditTable,
  },
  mixins: [
    MixinMatching,
  ],
  props: {
    getCategoriesRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookGetCategories || null,
    },
  },
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
        matched: this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress?.matched || '--',
        total: this.GET_CATEGORY_MATCHING_SUMMARY.matchingProgress?.total || '--',
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

    // @ts-ignore
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
        const res = await fetch(this.getCategoriesRoute, {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `id_category=${mainCategoryId}&page=${page}`,
        });
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        const json = await res.json();
        this.categories = this.setValuesFromRequest(json);
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
    md2html: (md) => (new Showdown.Converter()).makeHtml(md),
  },
});
</script>
