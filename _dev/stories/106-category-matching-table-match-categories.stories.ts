import tableMatching from '../src/components/category-matching/tableMatching.vue';

export default {
  title: 'Category matching/TableMatching',
  component: tableMatching,
};

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

export const TestWithAllCases:any = Template.bind({});
TestWithAllCases.args = {
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
      'deploy': false,
      'show': false,
      'categoryName': 'Bird Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '1/2/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'subcategoryId': 3,
      'propagation': false,
    },
    {
      'shopCategoryId': '3',
      'shopCategoryName': 'Bird Baths',
      'deploy': null,
      'show': false,
      'categoryName': 'Bird Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '1/2/3/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
      'subcategoryId': 3,
      'propagation': null,
    },
    {
      'shopCategoryId': "10",
      'shopCategoryName': 'Activewear',
      'shopParentCategoryIds': "10/",
      'deploy': false,
      'show': true,
      'categoryName': 'Apparel & Accessories',
      'categoryId': 166,
      'subcategoryName':'Apparel & Accessories > Clothing > Activewear',
      'subcategoryId': 5322,
      'propagation': false,
    },
    {
      'shopCategoryId': "11",
      'shopCategoryName': 'Bicycle',
      'shopParentCategoryIds': "10/11/",
      'deploy': false,
      'show': false,
      'categoryName': 'Apparel & Accessories',
      'categoryId': 166,
      'subcategoryName': 'Apparel & Accessories > Clothing > Activewear > Bicycle Activewear',
      'subcategoryId': 5697,
      'propagation': false,
    },
    {
      'shopCategoryId': "12",
      'shopCategoryName': 'Jerseys',
      'shopParentCategoryIds': "10/11/12/",
      'deploy': null,
      'show': false,
      'categoryName': 'Apparel & Accessories',
      'categoryId': 166,
      'subcategoryName': 'Apparel & Accessories > Clothing > Activewear > Bicycle Activewear > Bicycle Jerseys',
      'subcategoryId': 3455,
      'propagation': null,
    },
    {
      'shopCategoryId': '9',
      'shopCategoryName': 'Cat',
      'deploy': undefined,
      'show': false,
      'categoryName': 'Cat Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '4/9/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Cat Supplies',
      'subcategoryId': 3,
      'propagation': false,
    },
    {
      'shopCategoryId': "20",
      'shopCategoryName': 'Camera',
      'shopParentCategoryIds': "20/",
      'deploy': false,
      'show': true,
      'categoryName': 'Cameras & Optics',
      'categoryId': 141,
      'subcategoryName':'Cameras & Optics > Camera & Optic Accessories',
      'subcategoryId': 2096,
      'propagation': false,
    },
    {
      'shopCategoryId': "21",
      'shopCategoryName': 'Battery',
      'shopParentCategoryIds': "20/21/",
      'deploy': null,
      'show': false,
      'categoryName': 'Cameras & Optics',
      'categoryId': 141,
      'subcategoryName':'Cameras & Optics > Camera & Optic Accessories > Camera Parts & Accessories',
      'subcategoryId': 143,
      'propagation': null,
    },
    {
      'shopCategoryId': "30",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "30/",
      'deploy': null,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName':'Hardware > Building Consumables',
      'subcategoryId': 503739,
      'propagation': null,
    },

  ],
  testGetCurrentRow() {
    return [];
  }
}
