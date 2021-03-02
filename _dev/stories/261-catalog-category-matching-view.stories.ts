import CategoryMatchingView from '../src/components/catalog/category-matching-view.vue';

export default {
  title: 'Catalog/Category matching view page',
  component: CategoryMatchingView,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryMatchingView},
  template: '<category-matching-view  :forceFetchData="testFetchData" :forceCategories="testFetchCategories"/>',
});

export const Default: any = Template.bind({});
Default.args = {
  testFetchData: {
    matchingProgress: {total: 2, matched: 0}
  },
  testFetchCategories: [
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': '',
      'googleCategoryParentId': 0,
      'shopParentCategoryIds': '',
      'googleCategoryName':'',
      'subcategoryId': 0,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '3',
      'shopCategoryName': 'Bird Baths',
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': '',
      'googleCategoryParentId': 0,
      'shopParentCategoryIds': '',
      'googleCategoryName':'',
      'subcategoryId': 0,
      'isParentCategory': false,
    }
  ]
};

export const ExportDone: any = Template.bind({});
ExportDone.args = {
  testFetchData: {
    matchingProgress: {total: 2, matched: 2}
  },
  testFetchCategories: [
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Bird Supplies',
      'googleCategoryParentId': 2,
      'shopParentCategoryIds': '',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'googleCategoryId': 3,
      'isParentCategory': true,
    },
    {
      'shopCategoryId': '3',
      'shopCategoryName': 'Bird Baths',
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Animals & Pet Supplies',
      'googleCategoryParentId': 2,
      'shopParentCategoryIds': '',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies',
      'googleCategoryId': 499954,
      'isParentCategory': true,
    }
  ]
};
