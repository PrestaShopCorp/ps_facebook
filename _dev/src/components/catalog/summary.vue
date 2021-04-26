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
  <spinner v-if="loading" />
  <div
    v-else
    id="catalogSummary"
  >
    <b-card class="card m-3">
      <export-catalog
        :validation="validation"
        :export-done-once="exportDone"
        :export-on="exportOn"
        :catalog-id="catalogId"
      />
    </b-card>

    <b-card
      class="card m-3"
    >
      <categories-matched
        v-if="matchingDone"
        :matching-progress="matchingProgress"
      />
      <match-categories
        v-else
        :is-primary-action="exportDone"
      />
    </b-card>

    <survey />
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard} from 'bootstrap-vue';
import Spinner from '../spinner/spinner.vue';

import PAGES from './pages';
import ExportCatalog from './summary/export-catalog.vue';
import MatchCategories from './summary/match-categories.vue';
import CategoriesMatched from './summary/categories-matched.vue';
import Survey from '../survey/survey.vue';

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
  props: {
    data: {
      type: Object,
      required: false,
      default: null,
    },
    catalogSummaryRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookGetCatalogSummaryRoute || null,
    },
  },
  data() {
    return {
      PAGES,
      loading: true,
      exportDone: this.data ? this.data.exportDone : false,
      exportOn: this.data ? this.data.exportOn : false,
      matchingDone: this.data ? this.data.matchingDone : false,
      matchingProgress: this.data ? this.data.matchingProgress : null,
      validation: this.data ? this.data.validation : null,
    };
  },
  created() {
    if (!this.data) {
      this.fetchData();
    } else {
      this.loading = false;
    }
  },
  computed: {
    catalogId() {
      if (this.$root.contextPsFacebook && this.$root.contextPsFacebook.catalog) {
        return this.$root.contextPsFacebook.catalog.id;
      }
      return null;
    },
  },
  methods: {
    fetchData() {
      this.loading = true;

      fetch(this.catalogSummaryRoute, {
        method: 'GET',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        this.exportDone = res.exportDone;
        this.exportOn = res.exportOn;
        this.matchingDone = res.matchingDone;
        this.matchingProgress = res.matchingProgress;
        this.validation = res.validation;
        this.loading = false;
      }).catch((error) => {
        console.error(error);
        this.loading = false;
      });
    },
    goto(page) {
      return this.$parent && this.$parent.goto(page);
    },
    back() {
      return this.$parent && this.$parent.back();
    },
  },
});
</script>

<style lang="scss" scoped>
  .card {
    border: none;
    border-radius: 3px;
    overflow: hidden;

    & > .card-body {
      padding: 1rem;
    }
  }
</style>
