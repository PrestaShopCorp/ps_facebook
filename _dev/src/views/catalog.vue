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
  <div
    id="catalog"
    class="ps-facebook-catalog-tab"
  >
    <catalog-summary v-if="currentPage === PAGES.summary" />
    <catalog-category-matching-edit v-if="currentPage === PAGES.categoryMatchingEdit" />
    <catalog-category-matching-view v-if="currentPage === PAGES.categoryMatchingView" />
    <catalog-report-details
      v-if="(currentPage === PAGES.prevalidationDetails) || (currentPage === PAGES.reportDetails)"
      :forceView="currentPage === PAGES.prevalidationDetails ? 'PREVALIDATION' : 'REPORTING'"
    />
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';

import PAGES from '../components/catalog/pages';
import CatalogSummary from '../components/catalog/summary.vue';
import CatalogCategoryMatchingEdit from '../components/catalog/category-matching-edit.vue';
import CatalogCategoryMatchingView from '../components/catalog/category-matching-view.vue';
import CatalogReportDetails from '../components/catalog/report-details.vue';

export default defineComponent({
  name: 'Catalog',
  components: {
    CatalogSummary,
    CatalogCategoryMatchingEdit,
    CatalogCategoryMatchingView,
    CatalogReportDetails,
  },
  props: {
    forcePage: {
      type: String,
      required: false,
      default: null,
    },
  },
  data() {
    const forcePage = (this.$route.query && this.$route.query.page) || this.forcePage;
    if (forcePage) { // consumes query if any, to let history clean
      const replacement = new URL(window.location);
      replacement.hash = '/catalog';
      window.location.replace(replacement.toString());
    }
    return {
      PAGES,
      currentPage: forcePage || PAGES.summary,
      historyStack: (forcePage && forcePage !== PAGES.summary)
        ? [this.forcePage]
        : [PAGES.summary],
    };
  },
  methods: {
    goto(page, replace = false) {
      if (replace) {
        this.historyStack[this.historyStack.length - 1] = page;
      } else {
        this.historyStack.push(page);
      }
      this.currentPage = page;
    },
    back() {
      if (this.historyStack.length > 1) {
        this.historyStack.pop();
        this.currentPage = this.historyStack[this.historyStack.length - 1];
      } else {
        window.history.back();
      }
    },
  },
});
</script>

<style lang="scss">
  .ps-facebook-catalog-tab {
    div.card:not(.survey) {
      border: none !important;
      border-radius: 3px;
    }
  }
</style>
