import CategoryAutocomplete from '../src/components/category-matching/category-autocomplete.vue';

export default {
  title: 'Catalog/Category matching edit page/CategoryAutocomplete component',
  component: CategoryAutocomplete,
};

const params = ':language="language" :shopCategoryId="shopCategoryId" '
  + ':initialCategoryName="initialCategoryName" :initialCategoryId="initialCategoryId" '
  + ':parentCategoryId="parentCategoryId" :autocompletionApi="autocompletionApi" ';
const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryAutocomplete},
  template: `<category-autocomplete ${params} @onCategorySelected="onCategorySelected" />`,
});

export const Default: any = Template.bind({});
Default.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: 'My initial category',
  initialCategoryId: 7,
  parentCategoryId: null,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};

export const Void: any = Template.bind({});
Void.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
  parentCategoryId: null,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};

export const French: any = Template.bind({});
French.args = {
  language: 'fr-FR',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
  parentCategoryId: null,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};

export const Subcategory: any = Template.bind({});
Subcategory.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
  parentCategoryId: 1,
  autocompletionApi: 'https://facebook-api.psessentials.net/taxonomy/',
};
