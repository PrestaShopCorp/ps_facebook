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
        <b-th>{{ $t('syncReport.error') }}</b-th>
        <b-th>{{ $t('syncReport.action') }}</b-th>
      </b-tr>
    </b-thead>
    <b-tr v-if="rows.length === 0" class="empty-cell">
      <b-td colspan="4">
        RIEN A VOIR ! VISUEL A METTRE !
      </b-td>
    </b-tr>
    <b-tr
      v-for="({
        id_product, id_product_attribute, name, messages
      }, index) in rows" :key="index"
    >
      <b-td>
        {{ name }} {{ id_product_attribute }}
      </b-td>
      <b-td>
        LANGs
      </b-td>
      <b-td>
        {{ messages }}
      </b-td>
      <b-td class="float-right">
        <b-link
          :href="url.replace('/1?', `/${id_product}?`)"
          target="_blank"
        >
          <i class="material-icons">edit</i>
        </b-link>
      </b-td>
    </b-tr>
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
