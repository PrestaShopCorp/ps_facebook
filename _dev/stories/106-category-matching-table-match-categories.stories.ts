import tableMatching from '../src/components/category-matching/tableMatching.vue';

export default {
  title: 'Category matching/TableMatching',
  component: tableMatching,
};

/**
 * deploy : { true : children recup, false: children deplier, undefined: we dont know, null: aucun children}
 * shopParentCategoryIds : { floor: shopCategoryId/2/3 | null} en String
 */

const categories = [
    {
      'shopCategoryId': "1",
      'shopCategoryName': 'Animals',
      'shopParentCategoryIds': "1/",
      'deploy': undefined,
      'show': true,
      'categoryName': 'Animals & Pet Supplies',
      'categoryId': 1,
      'subcategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'subcategoryId': 7385,
      'propagation': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': undefined,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
      'propagation': false,
    },
]

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {tableMatching},
  template: '<table-matching :initialCategories="categories"/>',
});

export const Default:any = Template.bind({});
Default.args = {
  categories,
};

export const Void:any = Template.bind({});
Void.args = {
  categories,
}
