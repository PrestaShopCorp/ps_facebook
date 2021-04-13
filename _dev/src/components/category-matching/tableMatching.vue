<!--**
 * 2007-2021 PrestaShop and Contributors
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
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div class="display-table-matchingFb">
    <b-table-simple :responsive="true">
      <b-thead>
        <b-tr>
          <b-th>{{ $t('categoryMatching.tableMatching.firstTd') }}</b-th>
          <b-th>{{ $t('categoryMatching.tableMatching.thirdTd') }}</b-th>
          <b-th>{{ $t('categoryMatching.tableMatching.secondTd') }}</b-th>
          <b-th>{{ $t('categoryMatching.tableMatching.fourthTd') }}</b-th>
          <b-th />
        </b-tr>
      </b-thead>
      <b-tbody>
        <editing-row
          v-for="category in activeCategories"
          :key="category.shopCategoryId"
          :category-style="categoryStyle(category)"
          :is-main-category="isMainCategory(category)"
          :shop-category-id="category.shopCategoryId"
          :initial-category-name="category.googleCategoryParentName"
          :initial-category-id="category.googleCategoryParentId"
          :initial-subcategory-name="category.googleCategoryName"
          :initial-subcategory-id="category.googleCategoryId"
          :initial-propagation="category.isParentCategory"
          :autocompletion-api="'https://facebook-api.psessentials.net/taxonomy/'"
          :save-matching-callback="saveMatchingCallback"
          :language="language"
          @rowClicked="getCurrentRow"
          @propagationClicked="applyToAllChildren"
        >
          {{ category.shopCategoryName }}
        </editing-row>
      </b-tbody>
    </b-table-simple>
    <div
      class="psfb-lazy-loading"
      v-if="loading"
    >
      <div
        id="spinner"
      />
    </div>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import EditingRow from './editing-row.vue';
import MixinMatching from './matching.ts';

export default defineComponent({
  name: 'TableMatching',
  components: {
    EditingRow,
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
      language: this.$store.state.context.appContext.localeLang,
    };
  },
  methods: {
    saveMatchingCallback(category) {
      if (this.overrideGetCurrentRow) {
        return Promise.resolve(true);
      }
      const updateParent = Number(category.propagate);
      const currentCategory = this.findShopCategory(this.categories, category.shopCategoryId);

      currentCategory.googleCategoryId = category.fbSubcategoryId;
      currentCategory.googleCategoryName = category.fbSubcategoryName;
      currentCategory.googleCategoryParentName = category.fbCategoryName;
      currentCategory.googleCategoryParentId = category.fbCategoryId;
      this.applyToAllChildren(category.shopCategoryId, category.propagate);

      if (category.fbSubcategoryName === undefined) {
        category.fbSubcategoryName = '';
      }

      return fetch(this.saveParentStatement, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          category_id: category.shopCategoryId,
          google_category_id: category.fbSubcategoryId,
          google_category_name: category.fbSubcategoryName,
          google_category_parent_name: category.fbCategoryName,
          google_category_parent_id: category.fbCategoryId,
          update_children: updateParent,
        }),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        this.$parent.fetchCategoryMatchingCounters();
        return true;
      });
    },

    applyToAllChildren(shopCategoryId, event) {
      const currentCategory = this.findShopCategory(this.categories, shopCategoryId);
      this.categories.forEach(
        (child) => {
          if (child.shopParentCategoryIds.startsWith(currentCategory.shopParentCategoryIds)
            && child.shopCategoryId !== currentCategory.shopCategoryId) {
            if (event === true) {
              if (this.canShowCheckbox(child) === false) {
                child.isParentCategory = null;
              } else {
                child.isParentCategory = true;
              }
              child.googleCategoryId = currentCategory.googleCategoryId;
              child.googleCategoryName = currentCategory.googleCategoryName;
              child.googleCategoryParentName = currentCategory.googleCategoryParentName;
              child.googleCategoryParentId = currentCategory.googleCategoryParentId;
            } else {
              child.isParentCategory = this.canShowCheckbox(child) === false ? null : false;
            }
          }
        });
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
      this.loading = true;
      const subcategory = subcategories;
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      const categoryLevel = currentCategory.shopParentCategoryIds.split('/').length - 1;

      if (categoryLevel === 3) {
        return;
      }
      currentCategory.deploy = this.FOLD;

      if (this.overrideGetCurrentRow !== null) {
        this.formatDataFromRequest(subcategory, currentCategory, this.categories, indexCtg, true);
      }

      this.$parent.fetchCategories(currentCategory.shopCategoryId, 1).then((res) => {
        const resp = this.formatDataFromRequest(res, currentCategory, this.categories, indexCtg);
        this.categories = resp.categories;
        if (currentCategory.deploy === this.NO_CHILDREN) {
          currentCategory.isParentCategory = null;
        }
        currentCategory.deploy = resp.statement;
      });
      this.loading = false;
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
#psFacebookApp .display-table-matchingFb .table-responsive:after {
  height: 24.6rem !important;
  content: '';
  display: block;
}

.display-table-matchingFb .psfb-lazy-loading {
  width: 100%;
  height: 40px;
  #spinner {
    color: #fff;
    background-color: #fff;
    width: 3rem !important;
    height: 3rem !important;
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
