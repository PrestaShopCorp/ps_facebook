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
          {{ $t('syncReport.image') }}
          <span><tooltip :text="$t('syncReport.imageTooltip')" /></span>
        </b-th>
        <b-th>
          {{ $t('syncReport.description') }}
          <span><tooltip :text="$t('syncReport.descriptionTooltip')" /></span>
        </b-th>
        <b-th>
          {{ $t('syncReport.barcode') }}
          <span><tooltip :text="$t('syncReport.barcodeTooltip')" /></span>
        </b-th>
        <b-th>{{ $t('syncReport.price') }}</b-th>
      </b-tr>
    </b-thead>
    <b-tr
      v-if="dynamicRows.length === 0"
      class="empty-cell"
    >
      <b-td colspan="8">
        {{ $t('syncReport.prevalidationEmpty') }}
      </b-td>
    </b-tr>
    <template
      v-for=
        "({name, cover, d, isbn, price, l, id_product, id_product_attribute}, i) in getRows()"
    >
      <b-tr
        v-if="(i === 0 || id_product !== rows[i - 1].id_product) && id_product_attribute"
        :key="i + 'super'"
      >
        <b-td>{{ id_product }}</b-td>
        <b-td>
          <b-link
            v-if="!id_product_attribute"
            :href="url.replace('/1?', `/${id_product}?`)"
            target="_blank"
          >
            {{ name }}
          </b-link>
          <span v-else>
            {{ name }}
          </span>
        </b-td>
        <b-td />
        <b-td />
        <b-td />
        <b-td />
        <b-td />
      </b-tr>
      <b-tr
        :key="i"
        :class="id_product_attribute ? 'dashed' : ''"
      >
        <b-td>{{ id_product_attribute ? '' : id_product }}</b-td>
        <b-td :class="id_product_attribute ? 'pl-4' : ''">
          <b-link
            v-if="!id_product_attribute"
            :href="url.replace('/1?', `/${id_product}?`)"
            target="_blank"
          >
            {{ id_product_attribute ? variantLabel(i) : name }}
          </b-link>
          <span v-else>
            {{ id_product_attribute ? variantLabel(i) : name }}
          </span>
        </b-td>
        <b-td>
          <span v-for="lang in l" :key="lang" class="badge badge-secondary">{{ lang }}</span>
        </b-td>
        <b-td>
          <i v-if="cover" class="material-icons text-success">done</i>
          <i v-else class="material-icons text-danger">close</i>
        </b-td>
        <b-td>
          <i v-if="d" class="material-icons text-success">done</i>
          <i v-else class="material-icons text-danger">close</i>
        </b-td>
        <b-td>
          <i v-if="isbn" class="material-icons text-success">done</i>
          <i v-else class="material-icons text-danger">close</i>
        </b-td>
        <b-td>
          <i v-if="price" class="material-icons text-success">done</i>
          <i v-else class="material-icons text-danger">close</i>
        </b-td>
      </b-tr>
    </template>
    <b-tfoot v-if="loading">
      <b-td class="loader-cell" colspan="8">
        <div class="spinner" />
      </b-td>
    </b-tfoot>
    <b-tfoot v-else-if="paginationEnabled">
      <b-td class="text-center" colspan="8">
        <b-button
          variant="link"
          @click="loadNextPage"
        >
          {{ $t('syncReport.loadNextPage') }}
        </b-button>
      </b-td>
    </b-tfoot>
  </b-table-simple>
</template>

<script>
/* eslint-disable camelcase */
import {defineComponent} from '@vue/composition-api';
import {BTableSimple} from 'bootstrap-vue';
import Tooltip from '../../help/tooltip.vue';

export default defineComponent({
  name: 'PrevalidationTable',
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
  },
  data() {
    return {
      loading: false,
      lastPage: 0,
      dynamicRows: this.rows,
      paginationEnabled: true,
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
    getRows() {
      return this.dynamicRows.reduce((acc, allValues, i) => {
        const {
          language,
          has_cover,
          has_description_or_short_description,
          has_manufacturer_or_ean_or_upc_or_isbn,
          has_price_tax_excl,
          id_product,
          id_product_attribute,
          ...vals
        } = allValues;
        const previousIndex = acc.length - 1;

        if (i !== 0
          && acc[previousIndex].id_product === id_product
          && acc[previousIndex].id_product_attribute === id_product_attribute
        ) {
          acc[previousIndex].cover = acc[previousIndex].cover && (has_cover === '1');
          acc[previousIndex].d = acc[previousIndex].d && (has_description_or_short_description === '1');
          acc[previousIndex].isbn = acc[previousIndex].isbn && (has_manufacturer_or_ean_or_upc_or_isbn === '1');
          acc[previousIndex].price = acc[previousIndex].price && (has_price_tax_excl === '1');
          acc[previousIndex].l.push(language);
        } else {
          acc.push({
            cover: has_cover === '1',
            d: has_description_or_short_description === '1',
            isbn: has_manufacturer_or_ean_or_upc_or_isbn === '1',
            price: has_price_tax_excl === '1',
            id_product,
            id_product_attribute,
            ...vals,
            l: [language],
          });
        }
        return acc;
      }, []);
    },
    handleScroll() {
      const de = document.documentElement;
      if (this.loading === false && de.scrollTop + window.innerHeight === de.scrollHeight) {
        this.loadNextPage();
      }
    },
    loadNextPage() {
      console.log('Requesting a new page:', this.lastPage + 1);
      this.loading = true;
      this.$parent.fetchPrevalidation(this.lastPage + 1).then((displayLoadMoreButton) => {
        // no more elements to fetch, do not trigger handleScroll again.
        if (displayLoadMoreButton === false) {
          window.removeEventListener('scroll', this.handleScroll);
          this.paginationEnabled = false;
        }
        this.dynamicRows = this.rows;
        this.lastPage += 1;
      }).catch((error) => {
        console.error(error);
      }).then(() => {
        setTimeout(() => { // used to debounce handleScroll
          this.loading = false;
        }, 500);
      });
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

  tr.dashed > td {
    border-top: 1px dotted lightgrey !important;
  }

  tr.empty-cell > td {
    padding: 6rem 3rem;
    text-align: center;
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
