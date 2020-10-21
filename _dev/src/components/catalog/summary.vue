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
  <div v-if="loading" class="page-spinner" />
  <div v-else id="catalogSummary">
    <b-card class="card m-2">
      <catalog-exported v-if="exportDone" />
      <export-catalog v-else />
    </b-card>

    <b-card class="card m-2">
      <categories-matched v-if="matchingDone" :matchingProgress="matchingProgress" />
      <match-categories v-else />
    </b-card>

    <b-card class="card m-2">
      <reporting :reporting="reporting" />
    </b-card>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BButton, BCard} from 'bootstrap-vue';

import ExportCatalog from './summary/export-catalog.vue';
import CatalogExported from './summary/catalog-exported.vue';
import MatchCategories from './summary/match-categories.vue';
import CategoriesMatched from './summary/categories-matched.vue';
import Reporting from './summary/reporting.vue';

export default defineComponent({
  name: 'CatalogSummary',
  components: {
    BButton,
    BCard,
    ExportCatalog,
    CatalogExported,
    MatchCategories,
    CategoriesMatched,
    Reporting,
  },
  data() {
    return {
      PAGES: this.$parent.PAGES,
      loading: true,
      exportDone: false,
      matchingDone: false,
      matchingProgress: null,
      reporting: null,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      // TODO !0: load data to know this.exportDone, this.matchingDone, this.matchingProgress
      //  and this.reporting...
      this.loading = false;
    },
    goto(page) {
      this.$parent.goto(page);
    },
    back() {
      this.$parent.back();
    },
  },
});
</script>

<style lang="scss" scoped>
  .card {
    border: none;
    border-radius: 3px;
    overflow: hidden;
  }
</style>
