import { rest } from 'msw';
import CategoryMatchingView from '@/components/catalog/category-matching-view.vue';

export default {
  title: 'Catalog/Category matching view page',
  component: CategoryMatchingView,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryMatchingView},
  template: '<category-matching-view />',
});

export const Default: any = Template.bind({});
Default.args = {
  beforeMount(this: any) {
    this.$store.state.context.appContext.defaultCategory = {id_category: 1};
  },
};
Default.parameters = {
  msw: {
    handlers: [
      rest.post('/shop-bo-mocked-api', (req, res, ctx) => {
        const action = req.body.action;
        if (action === 'getCategories') {
          return res(
            ctx.json([
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
            ]),
          );
        }

        if (action === 'CategoryMappingCounters') {
          return res(
            ctx.json({
              matchingProgress: {
                matched: 0,
                total: 2,
              },
            }),
          );
        }
        return res(
          ctx.status(404)
        );
      }),
    ],
  },
};

export const ExportDone: any = Template.bind({});
ExportDone.args = {
  beforeMount(this: any) {
    this.$store.state.context.appContext.defaultCategory = {id_category: 1};
  },
};

ExportDone.parameters = {
  msw: {
    handlers: [
      rest.post('/shop-bo-mocked-api', (req, res, ctx) => {
        const action = req.body.action;
        if (action === 'getCategories') {
          return res(
            ctx.json([
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
            ]),
          );
        }

        if (action === 'CategoryMappingCounters') {
          return res(
            ctx.json({
              matchingProgress: {
                matched: 2,
                total: 2,
              },
            }),
          );
        }
        return res(
          ctx.status(404)
        );
      }),
    ],
  },
};
