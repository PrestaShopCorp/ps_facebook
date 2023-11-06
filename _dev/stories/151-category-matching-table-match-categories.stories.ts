import tableMatching from '../src/components/category-matching/tableMatching.vue';

export default {
  title: 'Category matching/TableMatching',
  component: tableMatching,
};

const testSubcategory1 = [
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
    'isParentCategory': false,
  },
  {
    'shopCategoryId': '3',
    'shopCategoryName': 'Bird Baths',
    'deploy': '0',
    'show': true,
    'googleCategoryParentName': 'Animals & Pet Supplies > Pet Supplies > Bird Supplie',
    'googleCategoryParentId': 2,
    'shopParentCategoryIds': '',
    'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
    'googleCategoryId': 499954,
    'isParentCategory': false,
  }
];

const testSubcategory2 = {
  'shopCategoryId': '3',
  'shopCategoryName': 'Bird Baths',
  'deploy': '0',
  'show': true,
  'googleCategoryParentName': 'Animals & Pet Supplies > Pet Supplies > Bird Supplie',
  'googleCategoryParentId': 2,
  'shopParentCategoryIds': '',
  'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
  'googleCategoryId': 499954,
  'isParentCategory': false,
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
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Animals & Pet Supplies',
      'googleCategoryParentId': 1,
      'googleCategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'googleCategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Hardware',
      'googleCategoryParentId': 632,
      'googleCategoryName': 'Hardware > Tools',
      'googleCategoryId': 1167,
      'isParentCategory': false,
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
      'deploy': 1,
      'show': true,
      'googleCategoryParentName': 'Animals & Pet Supplies',
      'googleCategoryParentId': 1,
      'googleCategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'googleCategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Hardware',
      'googleCategoryParentId': 632,
      'googleCategoryName': 'Hardware > Tools',
      'googleCategoryId': 1167,
      'isParentCategory': false,
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
      'shopParentCategoryIds': '1/',
      'deploy': 1,
      'show': true,
      'googleCategoryParentName': 'Animals & Pet Supplies',
      'googleCategoryParentId': 1,
      'googleCategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'googleCategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': '0',
      'show': false,
      'googleCategoryParentName': 'Bird Supplies',
      'googleCategoryParentId': 2,
      'shopParentCategoryIds': '1/2/',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'googleCategoryId': 3,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Hardware',
      'googleCategoryParentId': 632,
      'googleCategoryName': 'Hardware > Tools',
      'googleCategoryId': 1167,
      'isParentCategory': false,
    },
  ],
  testGetCurrentRow() {
    return testSubcategory2;
  }
}

export const TestWithAllCases:any = Template.bind({});
TestWithAllCases.args = {
  categories: [
    {
      'shopCategoryId': "1",
      'shopCategoryName': 'Animals',
      'shopParentCategoryIds': "1/",
      'deploy': 3,
      'show': true,
      'googleCategoryParentName': 'Animals & Pet Supplies',
      'googleCategoryParentId': 1,
      'googleCategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'googleCategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': 3,
      'show': false,
      'googleCategoryParentName': 'Bird Supplies',
      'googleCategoryParentId': 2,
      'shopParentCategoryIds': '1/2/',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'googleCategoryId': 3,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '3',
      'shopCategoryName': 'Bird Baths',
      'deploy': '0',
      'show': false,
      'googleCategoryParentName': 'Bird Supplies',
      'googleCategoryParentId': 2,
      'shopParentCategoryIds': '1/2/3/',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
      'googleCategoryId': 3,
      'isParentCategory': null,
    },
    {
      'shopCategoryId': "10",
      'shopCategoryName': 'Activewear',
      'shopParentCategoryIds': "10/",
      'deploy': 3,
      'show': true,
      'googleCategoryParentName': 'Apparel & Accessories',
      'googleCategoryParentId': 166,
      'googleCategoryName':'Apparel & Accessories > Clothing > Activewear',
      'googleCategoryId': 5322,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "11",
      'shopCategoryName': 'Bicycle',
      'shopParentCategoryIds': "10/11/",
      'deploy': 3,
      'show': false,
      'googleCategoryParentName': 'Apparel & Accessories',
      'googleCategoryParentId': 166,
      'googleCategoryName': 'Apparel & Accessories > Clothing > Activewear > Bicycle Activewear',
      'googleCategoryId': 5697,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "12",
      'shopCategoryName': 'Jerseys',
      'shopParentCategoryIds': "10/11/12/",
      'deploy': '0',
      'show': false,
      'googleCategoryParentName': 'Apparel & Accessories',
      'googleCategoryParentId': 166,
      'googleCategoryName': 'Apparel & Accessories > Clothing > Activewear > Bicycle Activewear > Bicycle Jerseys',
      'googleCategoryId': 3455,
      'isParentCategory': null,
    },
    {
      'shopCategoryId': '9',
      'shopCategoryName': 'Cat',
      'deploy': 1,
      'show': false,
      'googleCategoryParentName': 'Cat Supplies',
      'googleCategoryParentId': 2,
      'shopParentCategoryIds': '4/9/',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies > Cat Supplies',
      'googleCategoryId': 3,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "20",
      'shopCategoryName': 'Camera',
      'shopParentCategoryIds': "20/",
      'deploy': 3,
      'show': true,
      'googleCategoryParentName': '',
      'googleCategoryParentId': 0,
      'googleCategoryName':'',
      'googleCategoryId': 0,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "21",
      'shopCategoryName': 'Battery',
      'shopParentCategoryIds': "20/21/",
      'deploy': '0',
      'show': false,
      'googleCategoryParentName': '',
      'googleCategoryParentId': 0,
      'googleCategoryName':'',
      'googleCategoryId': 0,
      'isParentCategory': null,
    },
    {
      'shopCategoryId': "30",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "30/",
      'deploy': '0',
      'show': true,
      'googleCategoryParentName': 'Hardware',
      'googleCategoryParentId': 632,
      'googleCategoryName':'Hardware > Building Consumables',
      'googleCategoryId': 503739,
      'isParentCategory': null,
    },

  ],
  testGetCurrentRow() {
    return [];
  }
}
