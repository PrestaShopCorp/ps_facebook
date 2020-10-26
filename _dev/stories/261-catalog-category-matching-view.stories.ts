import CategoryMatchingView from '../src/components/catalog/category-matching-view.vue';

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

};
// TODO
