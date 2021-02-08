import Vue from 'vue';

const mixin = Vue.extend({
  data() {
    return {
      NO_CHILDREN: '0',
      HAS_CHILDREN: '1',
      UNFOLD: '2',
      FOLD: '3',
    };
  },
  methods: {
    categoryStyle(category): String {
      const floor = category.shopParentCategoryIds.split('/').length - 1;
      let isDeployed = '';

      if (category.deploy === this.FOLD) {
        isDeployed = 'opened';
      } else if (category.deploy === this.NO_CHILDREN || floor === 3) {
        isDeployed = '';
      } else {
        isDeployed = 'closed';
      }

      return `psfb-match array-tree-lvl-${floor.toString()} ${isDeployed}`;
    },
    canShowCheckbox(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1;

      if (category.deploy === this.NO_CHILDREN || floor === 3) {
        return false;
      }

      return true;
    },

    findShopCategory(categories, shopCategoryId): Object {
      return categories.find(
        (child) => child.shopCategoryId === shopCategoryId,
      );
    },

    formatDataFromRequest(request, currentCategory, categories, indexCtg, forStorybook = false) {
      const subcategory = request;

      if (Array.isArray(subcategory)) {
        subcategory.forEach((el) => {
          categories.splice(indexCtg, 0, el);
          el.show = true;
          el.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + el.shopCategoryId}/`;
          el.isParentCategory = this.canShowCheckbox(el) ? false : null;
        });
      } else {
        categories.splice(indexCtg, 0, subcategory);
        subcategory.show = true;
        subcategory.shopParentCategoryIds = `${currentCategory.shopParentCategoryIds + subcategory.shopCategoryId}/`;
        subcategory.isParentCategory = this.canShowCheckbox(subcategory) ? false : null;
      }

      if (forStorybook === true) {
        return null;
      }

      currentCategory.deploy = subcategory.length !== 0 ? this.FOLD : this.NO_CHILDREN;

      return {
        statement: currentCategory.deploy,
        categories,
      };
    },

    formatDataFromLazyLoading(request, categories) {
      let hasCategories;

      if (Array.isArray(request)) {
        request.forEach((el) => {
          if (undefined === this.findShopCategory(categories, el.shopCategoryId)) {
            hasCategories = true;
            categories.push(el);
            /* eslint no-param-reassign: "error" */
            el.show = true;
            /* eslint no-param-reassign: "error" */
            el.shopParentCategoryIds = `${el.shopCategoryId}/`;
          }
          hasCategories = false;
        });
      } else {
        if (undefined === this.findShopCategory(categories, request.shopCategoryId)) {
          categories.push(request);
          hasCategories = true;
          /* eslint no-param-reassign: "error" */
          request.show = true;
          /* eslint no-param-reassign: "error" */
          request.shopParentCategoryIds = `${request.shopCategoryId}/`;
        }
        hasCategories = false;
      }

      return {
        newCategories: categories,
        hasCategoriesStatement: hasCategories,
      };
    },
  },
});

export default mixin;
