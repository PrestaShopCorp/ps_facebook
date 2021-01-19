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
        v-if="hasCategories"
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
      loading: null,
      enableCheckbox: false,
      filterCategory: false,
      hasCategories: false,
      numberOfCategoryWithoutMatching: 0,
    };
  },
  methods: {
    categoryStyle(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1;
      const isDeployed = category.deploy === this.FOLD ? 'opened' : (category.deploy === this.NO_CHILDREN || floor === 3 ? '' : 'closed');

      return `array-tree-lvl-${floor.toString()} ${isDeployed}`;
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

      this.setAction(currentCtg, subcategory);
    },

    changeFilter(value) {
      this.filterCategory = value;

      if (this.filterCategory === true) {
        this.categories.forEach((el) => {
          const result = !el.googleCategoryId;
          el.show = result;
        });
      } else {
        this.categories.forEach((el) => {
          el.show = true;
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
      const childrens = this.categories.filter(
        (child) => child.shopParentCategoryIds.startsWith(currentCategory.shopParentCategoryIds)
        && child.shopCategoryId !== currentCategory.shopCategoryId,
      );
      childrens.forEach((child) => {
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
      let subcategory = subcategories;
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      currentCategory.deploy = this.FOLD;

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
          currentCategory.deploy = this.FOLD;
        } else {
          currentCategory.deploy = this.NO_CHILDREN;
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
          }
          this.loading = false;
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
      if (!el.googleCategoryId) {
        this.numberOfCategoryWithoutMatching += 1;
      }
    });

    if (this.numberOfCategoryWithoutMatching === 0) {
      this.enableCheckbox = true;
    }
    window.addEventListener('scroll', this.handleScroll);
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
