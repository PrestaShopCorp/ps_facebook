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
  <div class="display-table-matchingFb">
    <b-table-simple :responsive="true">
      <b-thead>
        <b-tr>
          <b-th>Category on your site</b-th>
          <b-th>Facebook category</b-th>
          <b-th>Parent category</b-th>
          <b-th>Facebook subcategory</b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <editing-row
          v-for="category in categories"
          v-if="category.show"
          :key="category.shopCategoryId"
          :categoryStyle="categoryStyle(category)"
          :shopCategoryId="category.shopCategoryId"
          :initialCategoryName="category.categoryName"
          :initialCategoryId="category.categoryId"
          :initialSubcategoryName="category.subcategoryName"
          :initialSubcategoryId="category.subcategoryId"
          :initialPropagation="category.propagation"
          :autocompletionApi="'https://facebook-api.psessentials.net/taxonomy/'"
          :saveMatchingCallback="saveMatchingCallback"
          @rowClicked="getCurrentRow"
        >
          {{ category.shopCategoryName }}
        </editing-row>
      </b-tbody>
    </b-table-simple>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import EditingRow from './editing-row.vue';
/*
* rajouter une props dans editing row
*/
export default defineComponent({
  name: 'TableMatching',
  components: {
    EditingRow
  },
  mixins: [],
  props: {
    initialCategories: {
      type: Array,
      required: true
    },
    overrideGetCurrentRow: {
      type: Function,
      required: false,
      default: null
    }
  },
  computed: {
  },
  data() {
    return {
      categories: this.initialCategories,
    };
  },
  methods: {
    saveMatchingCallback() {
      return Promise.resolve(true);
    },
    categoryStyle(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1
      const isDeployed = category.deploy ? 'opened' : (category.deploy === null  || floor === 3 ? '' : 'closed')
      return 'array-tree-lvl-' + floor.toString() + ' ' + isDeployed
    },
    getCurrentRow(currentShopCategoryID) {
      if (this.overrideGetCurrentRow) {
        const result = this.overrideGetCurrentRow(currentShopCategoryID)
        var subcategory = result
      }

      const currentCategory = this.categories.find(element => element.shopCategoryId == currentShopCategoryID);
      const indexCtg = this.categories.indexOf(currentCategory) + 1;

      switch (currentCategory.deploy) {
        case true:
          const filterChildren = this.categories.filter(child =>
            child.shopParentCategoryIds.startsWith(currentCategory.shopParentCategoryIds + child.shopCategoryId + '/')
          )
          filterChildren.forEach(child => {
            child.show = false;
          })
          currentCategory.deploy = false;
        break;

        case false:
          const childrens = this.categories.filter(child =>
            child.shopParentCategoryIds.startsWith(currentCategory.shopParentCategoryIds + child.shopCategoryId + '/')
          )
          childrens.forEach(child => {
            child.show = true;
          })
          currentCategory.deploy = true;
        break;

        case undefined:
          currentCategory.deploy = true;
            if (Array.isArray(subcategory)) {
              subcategory.forEach(el => {
                this.categories.splice(indexCtg, 0, el);
                el.shopParentCategoryIds = currentCategory.shopParentCategoryIds + el.shopCategoryId + '/'
              })
            } else {
              this.categories.splice(indexCtg, 0, subcategory);
              subcategory.shopParentCategoryIds = currentCategory.shopParentCategoryIds + subcategory.shopCategoryId + '/'
            }
          currentCategory.deploy = true;
          // deploy = true
          // api PHP
          // cast deploy en toString pour le chevron regle css
          // si une reponse deploy = true
          // return (element, array empty => deploy => null)
        break;

        default:
      }
    }
  },
  created() {
    if (this.categories.length === 0) {
      // call php
    }
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
</style>
