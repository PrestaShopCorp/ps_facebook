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
  <b-table-simple>
    <b-thead>
      <b-tr>
        <b-th>{{ $t('syncReport.id') }}</b-th>
        <b-th>{{ $t('syncReport.name') }}</b-th>
        <b-th>
          {{ $t('syncReport.language') }}
          <span><tooltip :text="$t('syncReport.languageTooltip')" /></span>
        </b-th>
        <b-th>
          {{ $t('syncReport.error') }}
          <span><tooltip :text="$t('syncReport.errorTooltip')" /></span>
        </b-th>
      </b-tr>
    </b-thead>
    <b-tr v-if="rows.length === 0" class="empty-cell">
      <b-td colspan="5">
        {{ $t('syncReport.reportingEmpty') }}
      </b-td>
    </b-tr>
    <b-tr
      v-for="({
        id_product, id_product_attribute, name, messages
      }, index) in rows" :key="index"
    >
      <b-td>{{ id_product }}</b-td>
      <b-td>
        <b-link
          :href="url.replace('/1?', `/${id_product}?`)"
          target="_blank"
          v-if="id_product"
        >
          {{ name }}{{ id_product_attribute > 0 ? ` (#${id_product_attribute})` : '' }}
        </b-link>
        <span v-else>
          {{ name }}{{ id_product_attribute > 0 ? ` (#${id_product_attribute})` : '' }}
        </span>
      </b-td>
      <b-td>
        <span v-if="!!messages.base" class="badge badge-secondary">{{ locale.split('-')[0] }}</span>
        <span v-if="!!messages.l10n" class="badge badge-secondary">
          {{ $t('syncReport.otherLanguage') }}
        </span>
      </b-td>
      <b-td>
        <span v-if="Object.keys(messages).length === 1">{{ Object.values(messages)[0] }}</span>
        <ul v-else>
          <li v-for="(m, i) in Object.values(messages)" :key="i">
            {{ m }}
          </li>
        </ul>
      </b-td>
    </b-tr>
    <b-tfoot v-if="loading">
      <b-td class="loader-cell" colspan="8">
        <div class="spinner" />
      </b-td>
    </b-tfoot>
  </b-table-simple>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BTableSimple} from 'bootstrap-vue';
import Tooltip from '../../help/tooltip.vue';

export default defineComponent({
  name: 'ReportingTable',
  components: {
    BTableSimple,
    Tooltip,
  },
  props: {
    rows: {
      type: Array,
      required: false,
      default: () => [],
    },
    url: {
      type: String,
      required: true,
    },
    locale: {
      type: String,
      required: false,
      default: () => global.psFacebookLocale || 'en-US',
    },
  },
  data() {
    return {
      loading: false,
      lastPage: 0,
    };
  },
});
</script>

<style lang="scss" scoped>
  tr > th {
    white-space: nowrap;
  }

  tr.empty-cell > td {
    padding: 6rem 3rem;
    text-align: center;
  }

  tr > td > ul {
    margin-bottom: 0;
    padding-inline-start: 20px;
  }

  td.loader-cell {
    text-align: center;
    height: 0;
    padding: 0 !important;
    border-top: none !important;
    position: relative;

    & > div {
      position: absolute;
      top: 10px;
      height: 2rem !important;
      width: 2rem !important;
    }
  }

  span.badge {
    margin-right: 0.25rem;
    border-radius: 3px;
    text-transform: uppercase;
  }
</style>
