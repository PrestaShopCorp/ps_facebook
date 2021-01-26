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
    categoryStyle(category) {
      const floor = category.shopParentCategoryIds.split('/').length - 1;
      const isDeployed = category.deploy === this.FOLD ? 'opened' : (category.deploy === this.NO_CHILDREN || floor === 3 ? '' : 'closed');

      return `psfb-match array-tree-lvl-${floor.toString()} ${isDeployed}`;
    },
  },
});

export default mixin;
