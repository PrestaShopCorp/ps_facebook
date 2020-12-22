import tableMatching from '../src/components/category-matching/tableMatching.vue';

export default {
  title: 'Category matching/TableMatching',
  component: tableMatching,
};

const testSubcategory1 = [
  {
    'shopCategoryId': '2',
    'shopCategoryName': 'Bird',
    'deploy': 0,
    'show': true,
    'categoryName': 'Bird Supplies',
    'categoryId': 2,
    'shopParentCategoryIds': '',
    'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
    'subcategoryId': 3,
    'isParentCategory': false,
  },
  {
    'shopCategoryId': '3',
    'shopCategoryName': 'Bird Baths',
    'deploy': 0,
    'show': true,
    'categoryName': 'Animals & Pet Supplies > Pet Supplies > Bird Supplie',
    'categoryId': 2,
    'shopParentCategoryIds': '',
    'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
    'subcategoryId': 499954,
    'isParentCategory': false,
  }
];

const testSubcategory2 = {
  'shopCategoryId': '3',
  'shopCategoryName': 'Bird Baths',
  'deploy': 0,
  'show': true,
  'categoryName': 'Animals & Pet Supplies > Pet Supplies > Bird Supplie',
  'categoryId': 2,
  'shopParentCategoryIds': '',
  'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
  'subcategoryId': 499954,
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
      'deploy': 0,
      'show': true,
      'categoryName': 'Animals & Pet Supplies',
      'categoryId': 1,
      'subcategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'subcategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': 0,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
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
      'categoryName': 'Animals & Pet Supplies',
      'categoryId': 1,
      'subcategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'subcategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': 0,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
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
      'shopParentCategoryIds': "1/",
      'deploy': 3,
      'show': true,
      'categoryName': 'Animals & Pet Supplies',
      'categoryId': 1,
      'subcategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'subcategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': 1,
      'show': false,
      'categoryName': 'Bird Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '1/2/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'subcategoryId': 3,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "632",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "632/",
      'deploy': 0,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName': 'Hardware > Tools',
      'subcategoryId': 1167,
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
      'categoryName': 'Animals & Pet Supplies',
      'categoryId': 1,
      'subcategoryName':'Pet Supplies > Bird Supplies > Bird Cage Accessories',
      'subcategoryId': 7385,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '2',
      'shopCategoryName': 'Bird',
      'deploy': 3,
      'show': false,
      'categoryName': 'Bird Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '1/2/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
      'subcategoryId': 3,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': '3',
      'shopCategoryName': 'Bird Baths',
      'deploy': 0,
      'show': false,
      'categoryName': 'Bird Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '1/2/3/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories > Bird Cage Bird Baths',
      'subcategoryId': 3,
      'isParentCategory': null,
    },
    {
      'shopCategoryId': "10",
      'shopCategoryName': 'Activewear',
      'shopParentCategoryIds': "10/",
      'deploy': 3,
      'show': true,
      'categoryName': 'Apparel & Accessories',
      'categoryId': 166,
      'subcategoryName':'Apparel & Accessories > Clothing > Activewear',
      'subcategoryId': 5322,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "11",
      'shopCategoryName': 'Bicycle',
      'shopParentCategoryIds': "10/11/",
      'deploy': 3,
      'show': false,
      'categoryName': 'Apparel & Accessories',
      'categoryId': 166,
      'subcategoryName': 'Apparel & Accessories > Clothing > Activewear > Bicycle Activewear',
      'subcategoryId': 5697,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "12",
      'shopCategoryName': 'Jerseys',
      'shopParentCategoryIds': "10/11/12/",
      'deploy': 0,
      'show': false,
      'categoryName': 'Apparel & Accessories',
      'categoryId': 166,
      'subcategoryName': 'Apparel & Accessories > Clothing > Activewear > Bicycle Activewear > Bicycle Jerseys',
      'subcategoryId': 3455,
      'isParentCategory': null,
    },
    {
      'shopCategoryId': '9',
      'shopCategoryName': 'Cat',
      'deploy': 1,
      'show': false,
      'categoryName': 'Cat Supplies',
      'categoryId': 2,
      'shopParentCategoryIds': '4/9/',
      'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Cat Supplies',
      'subcategoryId': 3,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "20",
      'shopCategoryName': 'Camera',
      'shopParentCategoryIds': "20/",
      'deploy': 3,
      'show': true,
      'categoryName': 'Cameras & Optics',
      'categoryId': 141,
      'subcategoryName':'Cameras & Optics > Camera & Optic Accessories',
      'subcategoryId': 2096,
      'isParentCategory': false,
    },
    {
      'shopCategoryId': "21",
      'shopCategoryName': 'Battery',
      'shopParentCategoryIds': "20/21/",
      'deploy': 0,
      'show': false,
      'categoryName': 'Cameras & Optics',
      'categoryId': 141,
      'subcategoryName':'Cameras & Optics > Camera & Optic Accessories > Camera Parts & Accessories',
      'subcategoryId': 143,
      'isParentCategory': null,
    },
    {
      'shopCategoryId': "30",
      'shopCategoryName': 'Arduino',
      'shopParentCategoryIds': "30/",
      'deploy': 0,
      'show': true,
      'categoryName': 'Hardware',
      'categoryId': 632,
      'subcategoryName':'Hardware > Building Consumables',
      'subcategoryId': 503739,
      'isParentCategory': null,
    },

  ],
  testGetCurrentRow() {
    return [];
  }
}
