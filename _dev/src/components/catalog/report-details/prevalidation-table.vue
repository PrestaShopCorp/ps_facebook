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
    <b-tr v-if="rows.length === 0" class="empty-cell">
      <b-td colspan="7">
        RIEN A VOIR ! VISUEL A METTRE !
      </b-td>
    </b-tr>
    <template
      v-for="({
        name, has_cover, has_description_or_short_description,
        has_manufacturer_or_ean_or_upc_or_isbn,
        has_price_tax_excl, language, id_product, id_product_attribute
      }, index) in rows"
    >
      <template v-if="index === 0 || id_product !== rows[index - 1].id_product">
        <b-tr :key="index">
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
            >
              <i class="material-icons">edit</i>
            </b-link>
          </b-td>
        </b-tr>
        <b-tr
          :key="index + 'bis'"
          class="dashed"
        >
          <b-td class="pl-4">
            {{ variantLabel(index) }}
          </b-td>
          <b-td>{{ language }}</b-td>
          <b-td>
            <i
              v-if="has_cover === '0'"
              class="material-icons text-danger"
            >close</i>
            <i
              v-else
              class="material-icons text-success"
            >done</i>
          </b-td>
          <b-td>
            <i
              v-if="has_description_or_short_description === '0'"
              class="material-icons text-danger"
            >
              close
            </i>
            <i
              v-else
              class="material-icons text-success"
            >done</i>
          </b-td>
          <b-td>
            <i
              v-if="has_manufacturer_or_ean_or_upc_or_isbn === '0'"
              class="material-icons text-danger"
            >
              close
            </i>
            <i
              v-else
              class="material-icons text-success"
            >done</i>
          </b-td>
          <b-td>
            <i
              v-if="has_price_tax_excl === '0'"
              class="material-icons text-danger"
            >close</i>
            <i
              v-else
              class="material-icons text-success"
            >done</i>
          </b-td>
          <b-td />
        </b-tr>
      </template>
      <b-tr
        :key="index"
        v-else
        :class="!isNewVariant(index) ? 'none' : 'dashed'"
      >
        <b-td class="pl-4">
          {{ variantLabel(index) }}
        </b-td>
        <b-td>{{ language }}</b-td>
        <b-td>
          <i
            v-if="has_cover === '0'"
            class="material-icons text-danger"
          >close</i>
          <i
            v-else
            class="material-icons text-success"
          >done</i>
        </b-td>
        <b-td>
          <i
            v-if="has_description_or_short_description === '0'"
            class="material-icons text-danger"
          >
            close
          </i>
          <i
            v-else
            class="material-icons text-success"
          >done</i>
        </b-td>
        <b-td>
          <i
            v-if="has_manufacturer_or_ean_or_upc_or_isbn === '0'"
            class="material-icons text-danger"
          >
            close
          </i>
          <i
            v-else
            class="material-icons text-success"
          >done</i>
        </b-td>
        <b-td>
          <i
            v-if="has_price_tax_excl === '0'"
            class="material-icons text-danger"
          >close</i>
          <i
            v-else
            class="material-icons text-success"
          >done</i>
        </b-td>
        <b-td />
      </b-tr>
    </template>
  </b-table-simple>

</template>

<script>
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
    };
  },
  methods: {
    isNewVariant(index) {
      const attribute = this.rows[index].id_product_attribute;
      const product = this.rows[index].id_product;

      if (index > 0) {
        const previousAttribute = this.rows[index - 1].id_product_attribute;
        const previousProduct = this.rows[index - 1].id_product;
        if (attribute === previousAttribute && product === previousProduct) {
          return false;
        }
      }
      return true;
    },
    variantLabel(index) {
      // Show label only if previous variant in the array is different than the current one
      const attribute = this.rows[index].id_product_attribute;
      if (!attribute || !this.isNewVariant(index)) {
        return '';
      }
      return attribute;
    },
  },
});
</script>

<style lang="scss" scoped>
  th {
    text-transform: uppercase;
  }

  tr.dashed > td:first-of-type {
    border-top-style: dashed !important;
  }

  tr.none > td:first-of-type {
    border-top: 0px none !important;
  }

  tr.empty-cell > td {
    padding: 6rem 3rem;
    text-align: center;
  }
</style>
