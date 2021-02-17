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
        <b-th>{{ $t('syncReport.action') }}</b-th>
      </b-tr>
    </b-thead>
    <b-tr v-if="dynamicRows.length === 0" class="empty-cell">
      <b-td colspan="5">
        RIEN A VOIR ! VISUEL A METTRE !
      </b-td>
    </b-tr>
    <b-tr
      v-for="({
        id_product, id_product_attribute, name, messages
      }, index) in dynamicRows" :key="index"
      v-if="messages && Object.keys(messages).length > 0"
    >
      <b-td>{{ id_product }}</b-td>
      <b-td>
        {{ name }}{{ id_product_attribute > 0 ? ` (#${id_product_attribute})` : '' }}
      </b-td>
      <b-td>
        <span v-if="!!messages.base" class="badge badge-primary">{{ locale.split('-')[0] }}</span>
        <span v-if="!!messages.l10n" class="badge badge-primary">
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
      <b-td>
        <b-link
          :href="url.replace('/1?', `/${id_product}?`)"
          target="_blank"
        >
          <i class="material-icons">edit</i>
        </b-link>
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
      dynamicRows: this.rows,
    };
  },
  methods: {
    isNewVariant(index) {
      const attribute = this.dynamicRows[index].id_product_attribute;
      const product = this.dynamicRows[index].id_product;

      if (index > 0) {
        const previousAttribute = this.dynamicRows[index - 1].id_product_attribute;
        const previousProduct = this.dynamicRows[index - 1].id_product;
        if (attribute === previousAttribute && product === previousProduct) {
          return false;
        }
      }
      return true;
    },
    variantLabel(index) {
      // Show label only if previous variant in the array is different than the current one
      const attribute = this.dynamicRows[index].id_product_attribute;
      if (!attribute || !this.isNewVariant(index)) {
        return '';
      }
      return attribute;
    },
    handleScroll() {
      const de = document.documentElement;
      if (this.loading === false && de.scrollTop + window.innerHeight === de.scrollHeight) {
        console.log('Requesting a new page:', this.lastPage + 1);
        this.loading = true;
        this.$parent.fetchReporting(this.lastPage + 1).then((newPageCount) => {
          if (newPageCount === 0) { // no more elements to fetch, do not trigger handleScroll again.
            window.removeEventListener('scroll', this.handleScroll);
          }
          this.dynamicRows = this.rows;
          console.log('new elements:', newPageCount);
          this.lastPage += 1;
        }).catch((error) => {
          console.error(error);
        }).then(() => {
          setTimeout(() => { // used to debounce handleScroll
            this.loading = false;
          }, 500);
        });
      }
    },
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
});
</script>

<style lang="scss" scoped>
  tr > th {
    white-space: nowrap;
  }

  tr > th:last-of-type, tr > td:last-of-type {
    text-align: right;
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
  }

  span.badge {
    margin-right: 0.25rem;
    border-radius: 3px;
    text-transform: uppercase;
  }
</style>
