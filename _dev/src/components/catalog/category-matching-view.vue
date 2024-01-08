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
    id="catalogCategoryMatchingView"
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
        <i class="material-icons">keyboard_backspace</i>
        {{ $t('catalogSummary.backButton') }}
      </b-button>
      <div class="counter float-right ml-5">
        <h3 :class="matchingDone">
          {{ matchingProgress.matched }} / {{ matchingProgress.total }}
          <br>
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
        <i class="material-icons">keyboard_backspace</i>
        {{ $t('catalogSummary.backButton') }}
      </b-button>
      <h1>{{ $t('catalogSummary.categoryMatching') }}</h1>
      <div class="counter">
        <h3 :class="matchingDone">
          {{ matchingProgress.matched }} / {{ matchingProgress.total }}
          <br>
          <span>{{ $t('categoryMatching.counterSubTitle') }}</span>
        </h3>
      </div>
    </div>

    <p
      class="py-3"
      v-html="md2html($t('categoryMatching.intro'))"
    />

    <b-alert
      v-if="errors"
      show
      variant="danger"
    >
      {{ $t('categoryMatching.errors') }}
    </b-alert>

    <TableMatching
      v-if="categories"
      :initial-categories="categories"
    />
  </b-card>
</template>

<script>
import {defineComponent} from 'vue';
import Showdown from 'showdown';
import {BButton} from 'bootstrap-vue';
import TableMatching from '../category-matching/tableMatching.vue';
import LoadingPageSpinner from '@/components/spinner/loading-page-spinner.vue';
import MixinMatching from '../category-matching/matching.ts';

export default defineComponent({
  name: 'CatalogMatchingView',
  components: {
    BButton,
    LoadingPageSpinner,
    TableMatching,
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
    forceFetchData: {
      type: Object,
      required: false,
      default: null,
    },
    forceCategories: {
      type: Array,
      required: false,
      default: null,
    },
    getCategoryMappingStatusRoute: {
      type: String,
      required: false,
      default: () => window.psFacebookGetCategoryMappingStatus || null,
    },
  },
  computed: {
    matchingDone() {
      if (this.matchingProgress.matched === this.matchingProgress.total) {
        return 'matching-finished';
      }
      return '';
    },
  },
  data() {
    return {
      categories: [],
      loading: true,
      errors: false,
      matchingProgress: this.data ? this.data.matchingProgress : {total: '--', matched: '--'},
    };
  },
  created() {
    this.loading = true;
    this.fetchCategoryMatchingCounters();
    if (this.forceCategories !== null) {
      this.categories = this.forceCategories;
      this.loading = false;
    } else {
      this.fetchCategories(0, 1).then((res) => {
        this.categories = res;
        this.loading = false;
      });
    }

    // @ts-ignore
    this.$segment.identify(this.$store.state.context?.appContext?.shopId, {
      psx_ps_category_mapping_tool_clicked: true,
    });
  },
  methods: {
    fetchCategoryMatchingCounters() {
      fetch(this.getCategoryMappingStatusRoute)
        .then((res) => {
          if (this.forceFetchData !== null) {
            return this.forceFetchData;
          }
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        })
        .then((res) => {
          this.matchingProgress = (res && res.matchingProgress) || {total: '--', matched: '--'};
        }).catch((error) => {
          console.error(error);
        });
    },

    fetchCategories(idCategory, page) {
      let mainCategoryId = idCategory;

      if (mainCategoryId === 0) {
        mainCategoryId = this.$store.state.context.appContext.defaultCategory.id_category;
      }

      return fetch(this.getCategoriesRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_category=${mainCategoryId}&page=${page}`,
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      })
        .then((res) => this.setValuesFromRequest(res)).catch((error) => {
          this.errors = true;
          console.error(error);
        });
    },
    md2html: (md) => (new Showdown.Converter()).makeHtml(md),
  },
  watch: {
  },
});
</script>
