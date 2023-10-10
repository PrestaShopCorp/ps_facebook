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
    <catalog-summary v-if="$route.name === CatalogTabPages.summary" />
    <catalog-category-matching-edit v-if="$route.name === CatalogTabPages.categoryMatchingEdit" />
    <catalog-category-matching-view v-if="$route.name === CatalogTabPages.categoryMatchingView" />
    <catalog-report-details
      v-if="($route.name === CatalogTabPages.prevalidationDetails) || ($route.name === CatalogTabPages.reportDetails)"
      :force-view="$route.name === CatalogTabPages.prevalidationDetails ? 'PREVALIDATION' : 'REPORTING'"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

import CatalogTabPages from '@/components/catalog/pages';
import CatalogSummary from '@/components/catalog/catalog-summary.vue';
import CatalogCategoryMatchingEdit from '@/components/catalog/category-matching-edit.vue';
import CatalogCategoryMatchingView from '@/components/catalog/category-matching-view.vue';
import CatalogReportDetails from '@/components/catalog/report-details.vue';

export default defineComponent({
  name: 'CatalogTab',
  components: {
    CatalogSummary,
    CatalogCategoryMatchingEdit,
    CatalogCategoryMatchingView,
    CatalogReportDetails,
  },
  data() {
    return {
      CatalogTabPages,
    };
  },
});
</script>
