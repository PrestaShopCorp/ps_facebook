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
          :initial-category-name="category.googleCategoryParentName"
          :initial-category-id="category.googleCategoryParentId"
          :initial-subcategory-name="category.googleCategoryName"
          :initial-subcategory-id="category.googleCategoryId"
          :initial-propagation="category.isParentCategory"
          :autocompletion-api="'https://facebook-api.psessentials.net/taxonomy/'"
          :save-matching-callback="saveMatchingCallback"
          @rowClicked="getCurrentRow"
        >
          {{ category.shopCategoryName }}
        </editing-row>
      </b-tbody>
      <b-tfoot v-if="loading">
        <div
          v-if="hasCategories"
          class="spinner"
        />
        <b-tr v-else>
          <b-td
            colspan="4"
            style="text-align:center"
          >
            <div>
              {{ $t('categoryMatching.tableMatching.nocategoriesloaded') }}
            </div>
          </b-td>
        </b-tr>
      </b-tfoot>
    </b-table-simple>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import EditingRow from './editing-row.vue';

const PARENT_STATEMENT = {
  NO_CHILDREN: '0',
  HAS_CHILDREN: '1',
  UNFOLD: '2',
  FOLD: '3',
};

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
    saveParentStatement: {
      type: String,
      required: false,
      default: () => global.psFacebookUpdateCategoryMatch || null,
    },
    getCategoriesRoute: {
      type: String,
      required: false,
      default: () => global.psFacebookGetCategories || null,
    },
  },
  computed: {
  },
  data() {
    return {
      categories: this.initialCategories,
      loading: null,
      hasCategories: false,
    };
  },
  methods: {
    saveMatchingCallback(category) {
      // condition for work with storybook
      if (this.overrideGetCurrentRow) {
        return Promise.resolve(true);
      }
      console.log(category);

      return fetch(this.saveParentStatement, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `category_id=${category.shopCategoryId}&google_category_id=${category.fbSubcategoryId}&google_category_name=${category.fbSubcategoryName}&google_category_parent_name=${category.fbCategoryName}&google_category_parent_id=${category.fbCategoryId}&update_children=${category.propagate}`,
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return true;
      });
    },

    categoryStyle(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1;
      const isDeployed = category.deploy === PARENT_STATEMENT.FOLD ? 'opened' : (category.deploy === PARENT_STATEMENT.NO_CHILDREN || floor === 3 ? '' : 'closed');

      return `psfb-match array-tree-lvl-${floor.toString()} ${isDeployed}`;
    },

    canShowCheckbox(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1;

      if (category.deploy === PARENT_STATEMENT.NO_CHILDREN || floor === 3) {
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
      currentCategory.deploy = PARENT_STATEMENT.FOLD;
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
        if (child.deploy === PARENT_STATEMENT.FOLD) {
          child.deploy = PARENT_STATEMENT.UNFOLD;
        }
      });
      currentCategory.deploy = PARENT_STATEMENT.UNFOLD;
    },

    /**
    * add subcategories
    */
    addChildren(currentCategory, subcategories) {
      let subcategory = subcategories;
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      currentCategory.deploy = PARENT_STATEMENT.FOLD;

      // condition for work with storybook
      if (this.overrideGetCurrentRow) {
        if (Array.isArray(subcategory)) {
          subcategory.forEach((el) => {
            this.categories.splice(indexCtg, 0, el);
            el.show = true;
            el.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + el.shopCategoryId}/`;
          });
        } else {
          this.categories.splice(indexCtg, 0, subcategory);
          subcategory.show = true;
          subcategory.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + subcategory.shopCategoryId}/`;
        }
        return;
      }

      this.$parent.fetchCategories(currentCategory.shopCategoryId, 1).then((res) => {
        subcategory = res;
        if (Array.isArray(subcategory)) {
          subcategory.forEach((el) => {
            this.categories.splice(indexCtg, 0, el);
            el.show = true;
            el.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + el.shopCategoryId}/`;
          });
        } else {
          this.categories.splice(indexCtg, 0, subcategory);
          subcategory.show = true;
          subcategory.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + subcategory.shopCategoryId}/`;
        }

        if (subcategory.length !== 0) {
          currentCategory.deploy = PARENT_STATEMENT.FOLD;
        } else {
          currentCategory.deploy = PARENT_STATEMENT.NO_CHILDREN;
        }
      });
    },

    handleScroll() {
      if (document.documentElement.scrollTop + window.innerHeight
          === document.documentElement.scrollHeight
      ) {
        this.loading = true;
        this.$parent.fetchCategories(0, 1).then((res) => {
          if (Array.isArray(res)) {
            res.forEach((el) => {
              if (undefined === this.categories.find(
                (ctg) => el.shopCategoryId === ctg.shopCategoryId)
              ) {
                this.hasCategories = true;
                this.categories.push(el);
                el.show = true;
                el.shopParentCategoryIds = `${el.shopCategoryId}/`;
              }
              this.hasCategories = false;
            });
          } else {
            if (undefined === this.categories.find(
              (ctg) => res.shopCategoryId === ctg.shopCategoryId)
            ) {
              this.categories.push(res);
              this.hasCategories = true;
              res.show = true;
              res.shopParentCategoryIds = `${res.shopCategoryId}/`;
            }
            this.hasCategories = false;
            this.loading = false;
          }
        });
      }
    },
    /**
    * call function
    */
    setAction(currentCategory, subcategories) {
      const dictionary = {
        0: () => {},
        1: () => this.addChildren(currentCategory, subcategories),
        2: () => this.showChildren(currentCategory),
        3: () => this.hideChildren(currentCategory),
      };
      return dictionary[currentCategory.deploy].call();
    },
  },
  created() {
    window.addEventListener('scroll', this.handleScroll);
  },
  watch: {
  },
});
</script>
<style lang="scss" scoped>
.spinner {
  color: #fff;
  background-color: #fff;
  width: 1.3rem !important;
  height: 1.3rem !important;
  border-radius: 2.5rem;
  border-right-color: #25b9d7;
  border-bottom-color: #25b9d7;
  border-width: .1875rem;
  border-style: solid;
  font-size: 0;
  outline: none;
  display: inline-block;
  border-left-color: #bbcdd2;
  border-top-color: #bbcdd2;
  -webkit-animation: rotating 2s linear infinite;
  animation: rotating 2s linear infinite;
  position: absolute;
  left: calc(50% - 0.6rem);
}
</style>
