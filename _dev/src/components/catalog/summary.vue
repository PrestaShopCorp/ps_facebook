<!--**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div
    v-if="loading"
    class="page-spinner"
  />
  <div
    v-else
    id="catalogSummary"
  >
    <b-card class="card m-3">
      <categories-matched
        v-if="matchingDone"
        :matching-progress="matchingProgress"
      />
      <match-categories v-else />
    </b-card>

    <b-card class="card m-3">
      <catalog-exported v-if="exportDone" />
      <export-catalog
        v-else
        :is-primary-action="matchingDone"
      />
    </b-card>

    <b-card class="card m-3">
      <reporting :reporting="reporting" />
    </b-card>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard} from 'bootstrap-vue';

import PAGES from './pages';
import ExportCatalog from './summary/export-catalog.vue';
import CatalogExported from './summary/catalog-exported.vue';
import MatchCategories from './summary/match-categories.vue';
import CategoriesMatched from './summary/categories-matched.vue';
import Reporting from './summary/reporting.vue';

export default defineComponent({
  name: 'CatalogSummary',
  components: {
    BCard,
    ExportCatalog,
    CatalogExported,
    MatchCategories,
    CategoriesMatched,
    Reporting,
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
      matchingDone: this.data ? this.data.matchingDone : false,
      matchingProgress: this.data ? this.data.matchingProgress : null,
      reporting: this.data ? this.data.reporting : null,
    };
  },
  created() {
    if (!this.data) {
      this.fetchData();
    } else {
      this.loading = false;
    }
  },
  methods: {
    fetchData() {
      this.loading = true;

      fetch(this.catalogSummaryRoute, {
        method: 'GET',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        // body: JSON.stringify({}),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        this.exportDone = res.exportDone;
        this.matchingDone = res.matchingDone;
        this.matchingProgress = res.matchingProgress;
        this.reporting = res.reporting;
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
