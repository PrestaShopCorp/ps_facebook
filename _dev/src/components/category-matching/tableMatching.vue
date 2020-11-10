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
          <b-th>{{ $t('categoryMatching.tableMatching.firstTd') }}</b-th>
          <b-th>{{ $t('categoryMatching.tableMatching.secondTd') }}</b-th>
          <b-th>{{ $t('categoryMatching.tableMatching.thirdTd') }}</b-th>
          <b-th>{{ $t('categoryMatching.tableMatching.fourthTd') }}</b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <editing-row
          v-for="category in categories"
          v-if="category.show"
          :key="category.shopCategoryId"
          :category-style="categoryStyle(category)"
          :shop-category-id="category.shopCategoryId"
          :initial-category-name="category.categoryName"
          :initial-category-id="category.googleCategoryId"
          :initial-subcategory-name="category.subcategoryName"
          :initial-subcategory-id="category.googleCategoryId"
          :initial-propagation="category.isParentCategory"
          :autocompletion-api="'https://facebook-api.psessentials.net/taxonomy/'"
          :save-matching-callback="saveMatchingCallback"
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
    EditingRow,
  },
  mixins: [],
  props: {
    initialCategories: {
      type: Array,
      required: true,
    },
    overrideGetCurrentRow: {
      type: Function,
      required: false,
      default: null,
    },
    getChildrensOfParentRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookGetCategory || null,
    },
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
      const floor = category.shopParentCategoryIds.split('/').length - 1;

      let isDeployed = '';
      if (category.deploy !== true) {
        if (category.deploy !== null || floor !== 3) {
          isDeployed = 'closed';
        }
      } else {
        isDeployed = 'opened';
      }

      return `array-tree-lvl-${floor.toString()} ${isDeployed}`;
    },

    canShowCheckbox(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1;

      if (category.deploy === null || floor === 3) {
        return false;
      }

      return true;
    },

    getCurrentRow(currentShopCategoryID) {
      let subcategory;
      if (this.overrideGetCurrentRow) {
        const result = this.overrideGetCurrentRow(currentShopCategoryID);
        subcategory = result;
      }

      const currentCtg = this.categories.find(
        (child) => child.shopCategoryId === currentShopCategoryID,
      );

      if (this.canShowCheckbox(currentCtg) === false) {
        currentCtg.isParentCategory = null;
      }

      this.setAction(currentCtg, subcategory);
    },

    /**
    * show children
    */
    showChildren(currentCategory) {
      const filterChildren = this.categories.filter(
        (child) => child.shopParentCategoryIds.match(new RegExp(`^${currentCategory.shopParentCategoryIds}[0-9]+/$`)),
      );
      /* eslint no-param-reassign: "error" */
      filterChildren.forEach((child) => {
        /* eslint no-param-reassign: "error" */
        child.show = true;
        if (this.canShowCheckbox(child) === false) {
          /* eslint no-param-reassign: "error" */
          child.isParentCategory = null;
        }
      });
      currentCategory.deploy = true;
    },

    /**
    * hide children
    */
    hideChildren(currentCategory) {
      const childrens = this.categories.filter(
        (child) => child.shopParentCategoryIds.startsWith(currentCategory.shopParentCategoryIds)
        && child.shopCategoryId !== currentCategory.shopCategoryId,
      );
      childrens.forEach((child) => {
        child.show = false;
        if (child.deploy === true) {
          child.deploy = false;
        }
      });
      currentCategory.deploy = false;
    },

    /**
    * add subcategories
    */
    addChildren(currentCategory, subcategories) {
      let subcategory = subcategories;
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      currentCategory.deploy = true;

      fetch(this.getChildrensOfParentRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({id_category: currentCategory.shopCategoryId}),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      })
        .then((res) => {
          subcategory = res;
          console.log(res)
          if (Array.isArray(subcategory)) {
            subcategory.forEach((el) => {
              this.categories.splice(indexCtg, 0, el);
              el.show = true;
              el.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + el.shopCategoryId}/`;
            });
          } else {
            this.categories.splice(indexCtg, 0, subcategory);
            subcategory.show = true;
            subcategory.shopCategoryName = 'Test';
            subcategory.deploy = null;
            subcategory.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + subcategory.shopCategoryId}/`;
          }
          currentCategory.deploy = true;
        }).catch((error) => {
          console.error(error);
        });
    },

    /**
    * call function
    */
    setAction(currentCategory, subcategories) {
      const dictionary = {
        false: () => this.showChildren(currentCategory),
        true: () => this.hideChildren(currentCategory),
        undefined: () => this.addChildren(currentCategory, subcategories),
        null: () => {},
      };
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
