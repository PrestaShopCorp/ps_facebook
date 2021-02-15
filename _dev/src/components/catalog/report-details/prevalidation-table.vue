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
        <b-th>{{ $t('syncReport.name') }}</b-th>
        <b-th>{{ $t('syncReport.language') }}</b-th>
        <b-th>{{ $t('syncReport.image') }}</b-th>
        <b-th>{{ $t('syncReport.description') }}</b-th>
        <b-th>{{ $t('syncReport.barcode') }}</b-th>
        <b-th>{{ $t('syncReport.price') }}</b-th>
        <b-th>{{ $t('syncReport.action') }}</b-th>
      </b-tr>
    </b-thead>
    <b-tr
      v-if="dynamicRows.length === 0"
      class="empty-cell"
    >
      <b-td colspan="7">
        RIEN A VOIR ! VISUEL A METTRE !
      </b-td>
    </b-tr>
    <template
      v-for="({name, cover, desc, isbn, price, languages, id_product, id_product_attribute}, index) in groupRows()"
    >
      <b-tr
        v-if="(index === 0 || id_product !== rows[index - 1].id_product) && id_product_attribute"
        :key="index + 'super'"
      >
        <b-td>{{ name }}</b-td>
        <b-td />
        <b-td />
        <b-td />
        <b-td />
        <b-td />
        <b-td>
          <b-link
            :href="url.replace('/1?', `/${id_product}?`)"
            target="_blank"
            class="text-secondary"
          >
            <i class="material-icons">edit</i>
          </b-link>
        </b-td>
      </b-tr>
      <b-tr
        :key="index"
        :class="id_product_attribute ? 'dashed' : ''"
      >
        <b-td :class="id_product_attribute ? 'pl-4' : ''">
          {{ id_product_attribute ? variantLabel(index) : name }}
        </b-td>
        <b-td>
          <span v-for="l in languages" :key="l" class="badge badge-primary">{{ l }}</span>
        </b-td>
        <b-td>
          <i v-if="cover" class="material-icons text-success">done</i>
          <i v-else class="material-icons text-danger">close</i>
        </b-td>
        <b-td>
          <i v-if="desc" class="material-icons text-success">done</i>
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
        <b-td>
          <b-link
            v-if="!id_product_attribute"
            :href="url.replace('/1?', `/${id_product}?`)"
            target="_blank"
            class="text-secondary"
          >
            <i class="material-icons">edit</i>
          </b-link>
        </b-td>
      </b-tr>
    </template>
  </b-table-simple>
</template>

<script>
/* eslint-disable camelcase */
import {defineComponent} from '@vue/composition-api';
import {BTableSimple} from 'bootstrap-vue';

export default defineComponent({
  name: 'PrevalidationTable',
  components: {
    BTableSimple,
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
      dynamicRows: this.rows.map((row) => ({...row, page: 0})),
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
    groupRows() {
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
        if (i === 0) {
          acc.push({
            cover: has_cover === '1',
            desc: has_description_or_short_description === '1',
            isbn: has_manufacturer_or_ean_or_upc_or_isbn === '1',
            price: has_price_tax_excl === '1',
            id_product,
            id_product_attribute,
            ...vals,
            languages: [language],
          });
        } else {
          const {id_product: prevIdProd, id_product_attribute: prevIdProdAttr} = acc[i - 1];
          if (prevIdProd === id_product && prevIdProdAttr === id_product_attribute) {
            acc[i - 1].cover = acc[i - 1].cover && (has_cover === '1');
            acc[i - 1].desc = acc[i - 1].desc && (has_description_or_short_description === '1');
            acc[i - 1].isbn = acc[i - 1].isbn && (has_manufacturer_or_ean_or_upc_or_isbn === '1');
            acc[i - 1].price = acc[i - 1].price && (has_price_tax_excl === '1');
            acc[i - 1].languages.push(language);
          } else {
            acc.push({
              cover: has_cover === '1',
              desc: has_description_or_short_description === '1',
              isbn: has_manufacturer_or_ean_or_upc_or_isbn === '1',
              price: has_price_tax_excl === '1',
              id_product,
              id_product_attribute,
              ...vals,
              languages: [language],
            });
          }
        }
        return acc;
      }, []);
    },
  },
});
</script>

<style lang="scss" scoped>
  tr.dashed > td {
    border-top: 1px dotted lightgrey !important;
  }

  tr > th:last-of-type, tr > td:last-of-type {
    text-align: right;
  }

  tr.empty-cell > td {
    padding: 6rem 3rem;
    text-align: center;
  }

  span.badge {
    margin-right: 0.25rem;
    border-radius: 3px;
    text-transform: uppercase;
  }
</style>
