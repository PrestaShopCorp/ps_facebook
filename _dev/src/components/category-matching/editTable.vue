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
  <div class="display-table-editTable">
    <div class="d-none d-md-block">
      <b-checkbox
        :checked="filterCategory"
        class="float-left mr-3"
        @change="changeFilter"
        disabled-field="notEnabled"
        :disabled="enableCheckbox"
      >
        {{ $t('categoryMatching.editTable.checkboxTxt') }} ({{ numberOfCategoryWithoutMatching }})
      </b-checkbox>
    </div>
    <b-table-simple :responsive="true">
      <b-thead>
        <b-tr>
          <b-th>{{ $t('categoryMatching.editTable.psCategoryName') }}</b-th>
          <b-th>{{ $t('categoryMatching.editTable.fbCategoryName') }}</b-th>
          <b-th>{{ $t('categoryMatching.editTable.fbSubcategoryName') }}</b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <b-tr
          v-for="category in activeCategories"
          :key="category.shopCategoryId"
          :class="categoryStyle(category)"
        >
          <b-td
            @click="getCurrentRow(category.shopCategoryId)"
          >
            {{ category.shopCategoryName }}
          </b-td>
          <b-td v-if="!category.googleCategoryParentName">
            <b-badge variant="danger">
              {{ $t('categoryMatching.editTable.required') }}
            </b-badge>
          </b-td>
          <b-td v-else>
            {{ category.googleCategoryParentName }}
          </b-td>
          <b-td> {{ category.googleCategoryName ? category.googleCategoryName : '-' }} </b-td>
        </b-tr>
      </b-tbody>
    </b-table-simple>
    <b-tfoot v-if="loading">
      <div
        id="spinner"
      />
    </b-tfoot>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import MixinMatching from './matching.ts';

export default defineComponent({
  name: 'editTable',
  components: {
  },
  mixins: [
    MixinMatching,
  ],
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
    activeCategories() {
      return this.categories.filter(
        (category) => category.show,
      );
    },
  },
  data() {
    return {
      categories: this.initialCategories,
      loading: false,
      enableCheckbox: false,
      filterCategory: false,
      numberOfCategoryWithoutMatching: 0,
    };
  },
  methods: {
    getCurrentRow(currentShopCategoryID) {
      let subcategory = null;
      if (this.overrideGetCurrentRow) {
        const result = this.overrideGetCurrentRow(currentShopCategoryID);
        subcategory = result;
      }

      const currentCtg = this.categories.find(
        (child) => child.shopCategoryId === currentShopCategoryID,
      );

      this.setAction(currentCtg, subcategory);
    },

    changeFilter(value) {
      this.filterCategory = value;

      if (this.filterCategory === true) {
        const children = this.categories.filter(
          (child) => child.googleCategoryParentId,
        );
        children.forEach((el) => {
          el.show = false;
        });
      } else {
        const parents = this.isParentCategory(this.categories);
        this.categories.forEach((el) => {
          parents.forEach((node) => {
            if (node.shopCategoryId === el.shopCategoryId) {
              el.show = true;
              el.deploy = this.HAS_CHILDREN;
            }
            if (el.shopParentCategoryIds.startsWith(node.shopParentCategoryIds)
            && el.shopCategoryId !== node.shopCategoryId) {
              el.show = false;
            }
          });
        });
      }
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
      });
      currentCategory.deploy = this.FOLD;
    },

    /**
    * hide children
    */
    hideChildren(currentCategory) {
      const children = this.categories.filter(
        (child) => child.shopParentCategoryIds.startsWith(currentCategory.shopParentCategoryIds)
        && child.shopCategoryId !== currentCategory.shopCategoryId,
      );
      children.forEach((child) => {
        child.show = false;
        if (child.deploy === this.FOLD) {
          child.deploy = this.UNFOLD;
        }
      });
      currentCategory.deploy = this.UNFOLD;
    },

    /**
    * add subcategories
    */
    addChildren(currentCategory, subcategories) {
      const subcategory = subcategories;
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      const categoryLevel = currentCategory.shopParentCategoryIds.split('/').length - 1;

      if (categoryLevel === 3) {
        return;
      }
      currentCategory.deploy = this.FOLD;

      if (this.overrideGetCurrentRow !== null) {
        this.formatDataFromRequest(
          subcategory,
          currentCategory,
          this.categories,
          indexCtg,
          true,
        );
      }

      this.$parent.fetchCategories(currentCategory.shopCategoryId, 1).then((res) => {
        const resp = this.formatDataFromRequest(res, currentCategory, this.categories, indexCtg);
        this.categories = resp.categories;
        currentCategory.deploy = resp.statement;
      });
    },

    handleScroll() {
      const de = document.documentElement;
      if (this.loading === false && de.scrollTop + window.innerHeight
          === de.scrollHeight
      ) {
        this.loading = true;
        this.$parent.fetchCategories(0, 1).then((res) => {
          const resp = this.formatDataFromLazyLoading(res, this.categories);
          this.categories = resp.newCategories;
          if (resp.hasCategoriesStatement === true) {
            window.removeEventListener('scroll', this.handleScroll);
          }
        }).catch((error) => {
          console.error(error);
        }).then(() => {
          setTimeout(() => {
            this.loading = false;
          }, 500);
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
    this.categories.forEach((el) => {
      if (!el.googleCategoryParentId) {
        this.numberOfCategoryWithoutMatching += 1;
      }
    });

    if (this.numberOfCategoryWithoutMatching === 0) {
      this.enableCheckbox = true;
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
.display-table-editTable {
  table tbody tr {
    height: 50px;
  }
  .opened {
    td:first-child:before {
      font-family: Material Icons!important;
      font-weight: 400!important;
      font-style: normal!important;
      font-size: 24px!important;
      font-size: 1.5rem!important;
      line-height: 1!important;
      text-transform: none!important;
      letter-spacing: normal!important;
      word-wrap: normal!important;
      white-space: nowrap!important;
      direction: ltr!important;
      -webkit-font-smoothing: antialiased!important;
      text-rendering: optimizeLegibility!important;
      -moz-osx-font-smoothing: grayscale!important;
      font-feature-settings: "liga"!important;
      content: "expand_more"!important;
      border: none!important;
      display: inline-block!important;
      vertical-align: middle!important;
      width: auto!important;
      line-height: 0!important;
    }
    td:first-child {
      cursor: pointer!important;
    }
  }
  .closed {
    td:first-child:before {
      font-family: Material Icons!important;
      font-style: normal!important;
      font-size: 15px!important;
      font-size: 1.5rem!important;
      line-height: 1!important;
      text-transform: none!important;
      letter-spacing: normal!important;
      word-wrap: normal!important;
      white-space: nowrap!important;
      direction: ltr!important;
      -webkit-font-smoothing: antialiased!important;
      text-rendering: optimizeLegibility!important;
      -moz-osx-font-smoothing: grayscale!important;
      font-feature-settings: "liga"!important;
      content: "expand_less"!important;
      transform: rotate(90deg)!important;
      border: none!important;
      display: inline-block!important;
      vertical-align: middle!important;
      width: auto!important;
      line-height: 0!important;
    }
    td:first-child {
      cursor: pointer!important;
    }
  }

  .array-tree-lvl-2 {
    td:first-child {
      padding-left:40px!important;
    }
  }
  .array-tree-lvl-3 {
    td:first-child {
      padding-left:80px!important;
    }
  }

  #spinner {
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
}
</style>
