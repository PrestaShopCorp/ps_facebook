import { rest } from 'msw';
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
  },
};
Default.parameters = {
  msw: {
    handlers: [
      rest.post('/shop-bo-mocked-api', (req, res, ctx) => {
        const action = req.body.action;
        if (action === 'getCategories') {
          const idCategory = req.body.id_category;

          console.log(idCategory);

          if (idCategory === 1) {
            return res(
              ctx.json([
                {
                  shopCategoryId: "825-cat-lvl-2",
                  shopCategoryName: "Normal",
                  googleCategoryId: "3237",
                  googleCategoryName: "Live Animals",
                  googleCategoryParentId: "1",
                  googleCategoryParentName: "Animals - Pet Supplies",
                  isParentCategory: "1",
                  deploy: "1",
                },
                {
                  shopCategoryId: "826-cat-lvl-2",
                  shopCategoryName: "Fighting",
                  googleCategoryId: "7398",
                  googleCategoryName: "Pet Supplies > Bird Supplies > Bird Gyms - Playstands",
                  googleCategoryParentId: "1",
                  googleCategoryParentName: "Animals - Pet Supplies",
                  isParentCategory: "1",
                  deploy: "1",
                },
                {
                  shopCategoryId: "827-cat-lvl-2",
                  shopCategoryName: "Flying",
                  googleCategoryId: "3237",
                  googleCategoryName: "Live Animals",
                  googleCategoryParentId: "1",
                  googleCategoryParentName: "Animals - Pet Supplies",
                  isParentCategory: "0",
                  deploy: "1",
                },
                {
                  shopCategoryId: "828-cat-lvl-2",
                  shopCategoryName: "Poison",
                  googleCategoryId: "6",
                  googleCategoryName:
                    "Articles pour animaux de compagnie > Accessoires pour poissons",
                  googleCategoryParentId: "1",
                  googleCategoryParentName: "Animaux et articles pour animaux de compagnie",
                  isParentCategory: "0",
                  deploy: "1",
                },
                {
                  shopCategoryId: "829-cat-lvl-2",
                  shopCategoryName: "Ground",
                  googleCategoryId: "3237",
                  googleCategoryName: "Live Animals",
                  googleCategoryParentId: "1",
                  googleCategoryParentName: "Animals - Pet Supplies",
                  isParentCategory: "1",
                  deploy: "1",
                },
                {
                  shopCategoryId: "830-cat-lvl-2",
                  shopCategoryName: "Rock",
                  googleCategoryId: "3237",
                  googleCategoryName: "Live Animals",
                  googleCategoryParentId: "1",
                  googleCategoryParentName: "Animals - Pet Supplies",
                  isParentCategory: "0",
                  deploy: "1",
                },
                {
                  shopCategoryId: "831-cat-lvl-2",
                  shopCategoryName: "Bug",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "832-cat-lvl-2",
                  shopCategoryName: "Ghost",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "833-cat-lvl-2",
                  shopCategoryName: "Steel",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "834-cat-lvl-2",
                  shopCategoryName: "Fire",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "835-cat-lvl-2",
                  shopCategoryName: "Water",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "836-cat-lvl-2",
                  shopCategoryName: "Grass",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "837-cat-lvl-2",
                  shopCategoryName: "Electric",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "838-cat-lvl-2",
                  shopCategoryName: "Psychic",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "839-cat-lvl-2",
                  shopCategoryName: "Ice",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "840-cat-lvl-2",
                  shopCategoryName: "Dragon",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "841-cat-lvl-2",
                  shopCategoryName: "Dark",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "842-cat-lvl-2",
                  shopCategoryName: "Fairy",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "843-cat-lvl-2",
                  shopCategoryName: "Unknown",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
                {
                  shopCategoryId: "844-cat-lvl-2",
                  shopCategoryName: "Shadow",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
              ] as Category[]),
            );
          }

          if (idCategory?.endsWith('-cat-lvl-2')) {
            return res(
              ctx.json([
                {
                  shopCategoryId: `${idCategory}-999-cat-lvl-3`,
                  shopCategoryName: "Sub category",
                  googleCategoryId: null,
                  googleCategoryName: null,
                  googleCategoryParentId: null,
                  googleCategoryParentName: null,
                  isParentCategory: null,
                  deploy: "1",
                },
              ]),
            );
          }

          return res(
            ctx.json([]),
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