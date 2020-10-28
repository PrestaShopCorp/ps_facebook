import tableMatching from '../src/components/category-matching/tableMatching.vue';

export default {
  title: 'Category matching/TableMatching',
  component: tableMatching,
};

/**
 * deploy : { true : children recup, false: children deplier, undefined: we dont know, null: aucun children}
 * shopParentCategoryIds : { floor: shopCategoryId/2/3 | null} en String
 */

const testSubcategory1 = [
  {
    'shopCategoryId': '2',
    'shopCategoryName': 'Bird',
    'deploy': null,
    'show': true,
    'categoryName': 'Bird Supplies',
    'categoryId': 2,
    'shopParentCategoryIds': '',
    'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
    'subcategoryId': 3,
    'propagation': false,
  },
  {
    'shopCategoryId': '3',
    'shopCategoryName': 'Bird Baths',
    'deploy': null,
    'show': true,
    'categoryName': 'Animals & Pet Supplies > Pet Supplies > Bird Supplie',
    'categoryId': 2,
    'shopParentCategoryIds': '',
    'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
    'subcategoryId': 499954,
    'propagation': false,
  }
];

const testSubcategory2 = {
  'shopCategoryId': '3',
  'shopCategoryName': 'Bird Baths',
  'deploy': null,
  'show': true,
  'categoryName': 'Animals & Pet Supplies > Pet Supplies > Bird Supplie',
  'categoryId': 2,
  'shopParentCategoryIds': '',
  'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
  'subcategoryId': 499954,
  'propagation': false,
}



function testWithoutSubcategories(currentShopCategoryID) {
  return {};
}

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {tableMatching},
  template: '<table-matching :initialCategories="categories" :overrideGetCurrentRow="testGetCurrentRow" />',
});

export const Default:any = Template.bind({});
Default.args = {
  categories: [
    {
      'shopCategoryId': "1",
      'shopCategoryName': 'Animals',
      'shopParentCategoryIds': "1/",
      'deploy': null,
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
      'deploy': null,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
      'propagation': false,
    },
  ],
  testGetCurrentRow() {
    return {}
  }
};

export const TestWithOneChildren:any = Template.bind({});
TestWithOneChildren.args = {
  categories: [
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
      'deploy': null,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
      'propagation': false,
    },
  ],
  testGetCurrentRow() {
    return testSubcategory1;
  }
}

export const TestWithTwoChildren:any = Template.bind({});
TestWithTwoChildren.args = {
  categories: [
    {
      'shopCategoryId': "1",
      'shopCategoryName': 'Animals',
      'shopParentCategoryIds': "1/",
      'deploy': false,
      'show': true,
      'categoryName': 'Animals & Pet Supplies',
      'categoryId': 1,
      'subcategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'subcategoryId': 7385,
      'propagation': false,
    },
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': undefined,
      'show': false,
      'categoryName': 'Bird Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '1/2/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'subcategoryId': 3,
      'propagation': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': null,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
      'propagation': false,
    },
  ],
  testGetCurrentRow() {
    return testSubcategory2;
  }
}
