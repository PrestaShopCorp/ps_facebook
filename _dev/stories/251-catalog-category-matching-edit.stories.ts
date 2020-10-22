import CategoryMatchingEdit from '../src/components/catalog/category-matching-edit.vue';

export default {
  title: 'Catalog/Category matching edit page',
  component: CategoryMatchingEdit,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryMatchingEdit},
  template: '<category-matching-edit :data="data" />',
});

export const Default: any = Template.bind({});
Default.args = {
  data: {
    matchingProgress: {total: 42, matched: 32},
  },
};
// TODO
