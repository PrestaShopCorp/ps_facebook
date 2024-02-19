import CategoryMatchingEdit from '../src/components/catalog/category-matching-edit.vue';

export default {
  title: 'Catalog/Category matching edit page',
  component: CategoryMatchingEdit,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryMatchingEdit},
  template: '<category-matching-edit />',
  beforeMount: args.beforeMount,
});

export const Default: any = Template.bind({});
Default.args = {
  beforeMount(this: any) {
    this.$store.state.context.appContext.defaultCategory = {id_category: 1};
    this.$store.state.catalog.categoryMatching.matchingProgress = {total: 2, matched: 0};

    console.log(this.$store.state.catalog);
  },
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
      'shopParentCategoryIds': '2/',
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
      'shopParentCategoryIds': '3/',
      'googleCategoryName':'',
      'subcategoryId': 0,
      'isParentCategory': false,
    }
  ]
};

export const ExportDone: any = Template.bind({});
ExportDone.args = {

  beforeMount(this: any) {
    this.$store.state.context.appContext.defaultCategory = {id_category: 1};
    this.$store.state.catalog.categoryMatching.matchingProgress = {total: 2, matched: 0};
  },
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
      'shopParentCategoryIds': '2/',
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
      'shopParentCategoryIds': '3/',
      'googleCategoryName':'Animals & Pet Supplies > Pet Supplies',
      'googleCategoryId': 499954,
      'isParentCategory': true,
    }
  ]
};
