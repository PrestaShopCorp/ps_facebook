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
      let subcategory;
      if (this.overrideGetCurrentRow) {
        const result = this.overrideGetCurrentRow(currentShopCategoryID)
        subcategory = result
      }

      var currentCategory = this.categories.find(element => element.shopCategoryId == currentShopCategoryID);
      this.setAction(currentCategory, subcategory);
    },

    /**
    * show children
    */
    showChildren(currentCategory) {
      const childrens = this.categories.filter((child) => {
        let reg = new RegExp(currentCategory.shopParentCategoryIds + '\\d{1,}\\/+');
        let filter = reg.test(child.shopParentCategoryIds);

        if (filter === true &&
            child.shopParentCategoryIds !== currentCategory.shopParentCategoryIds &&
            child.show === true) {
          return child;
        }
      });

      childrens.forEach(child => {
        child.show = false;
      })

      currentCategory.deploy = false;
    },

    /**
    * hide children
    */
    hideChildren(currentCategory) {
      const filterChildren = this.categories.filter((child) => {
        let reg = new RegExp(currentCategory.shopParentCategoryIds + '\\d{1,}\\/+');
        let filter = reg.test(child.shopParentCategoryIds);

        if (filter === true &&
            child.shopParentCategoryIds !== currentCategory.shopParentCategoryIds &&
            child.show === false) {
          return child;
        }
      });

      filterChildren.forEach(child => {
        child.show = true;
      })
      currentCategory.deploy = true;
    },

    /**
    * add subcategories
    */
    addChildren(currentCategory, subcategories) {
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      currentCategory.deploy = true;
        if (Array.isArray(subcategories)) {
          subcategories.forEach(el => {
            this.categories.splice(indexCtg, 0, el);
            el.shopParentCategoryIds = currentCategory.shopParentCategoryIds + el.shopCategoryId + '/'
          })
        } else {
          this.categories.splice(indexCtg, 0, subcategories);
          subcategories.shopParentCategoryIds = currentCategory.shopParentCategoryIds + subcategories.shopCategoryId + '/'
        }
      currentCategory.deploy = true;
    },

    /**
    * call function
    */
    setAction(currentCategory, subcategories) {
      const dictionary = {
        false: () => this.hideChildren(currentCategory),
        true: () => this.showChildren(currentCategory),
        undefined: () => this.addChildren(currentCategory, subcategories),
        null: () => {},
      }
      return dictionary[currentCategory.deploy].call();
    },
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
