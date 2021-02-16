import CategoryMatchingView from '../src/components/catalog/category-matching-view.vue';

export default {
  title: 'Catalog/Category matching view page',
  component: CategoryMatchingView,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryMatchingView},
  template: '<category-matching-view  :overrideFetchData="testFetchData" :overrideCategories="testFetchCategories"/>',
});

const simpleTestWithMapping = [
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
];

const testMappingDone = [
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
];

const dataMatched = {
  matchingProgress: {total: 2, matched: 0}
};

const mappingDone = {
  matchingProgress: {total: 2, matched: 2}
};

export const Default: any = Template.bind({});
Default.args = {
  testFetchData() {
    return dataMatched;
  },
  testFetchCategories() {
    return simpleTestWithMapping;
  }
};

export const ExportDone: any = Template.bind({});
ExportDone.args = {
  testFetchData() {
    return mappingDone;
  },
  testFetchCategories() {
    return testMappingDone;
  }
};
