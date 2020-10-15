import CategoryAutocomplete from '../src/components/category-matching/category-autocomplete.vue';

export default {
  title: 'Category matching/CategoryAutocomplete component',
  component: CategoryAutocomplete,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryAutocomplete},
  template: '<category-autocomplete :language="language" :shopCategoryId="shopCategoryId" :initialCategoryName="initialCategoryName" :initialCategoryId="initialCategoryId" @onCategorySelected="onCategorySelected" />',
});
export const Default: any = Template.bind({});
Default.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: 'My initial category',
  initialCategoryId: 7,
};
export const Void: any = Template.bind({});
Void.args = {
  language: 'en-US',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
};
export const French: any = Template.bind({});
French.args = {
  language: 'fr-FR',
  shopCategoryId: '42',
  initialCategoryName: null,
  initialCategoryId: null,
};
