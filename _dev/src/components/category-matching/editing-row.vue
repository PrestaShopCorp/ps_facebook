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
  <b-tr>
    <b-td><slot/></b-td>
    <b-td>
      <category-autocomplete
        :language="language"
        :shopCategoryId="shopCategoryId"
        :initialCategoryName="initialCategoryName"
        :initialCategoryId="initialCategoryId"
      />
    </b-td>
    <b-td>Test</b-td>
  </b-tr>
</template>
<script lang="ts">
import {defineComponent} from '@vue/composition-api';
import {
  BTr,
  BTd,
} from 'bootstrap-vue';
import CategoryAutocomplete from './category-autocomplete.vue';

export default defineComponent({
  name: 'EditingRow',
  components: {
    BTr,
    BTd,
    CategoryAutocomplete,
  },
  mixins: [],
  props: {
    language: {
      type: String,
      required: false,
      default: 'en-US',
    },
    shopCategoryId: {
      type: String,
      required: true,
    },
    initialCategoryName: {
      type: String,
      required: false,
      default: null,
    },
    initialCategoryId: {
      type: Number,
      required: false,
      default: null,
    },
    initialSubcategoryName: {
      type: String,
      required: false,
      default: null,
    },
    initialSubcategoryId: {
      type: Number,
      required: false,
      default: null,
    },
  },
  data() {
    return {

    };
  },
  methods: {
  },
});
</script>
<style lang="scss" scoped>

</style>
