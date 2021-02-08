import CategoryMatchingView from '../src/components/catalog/category-matching-view.vue';

export default {
  title: 'Catalog/Category matching view page',
  component: CategoryMatchingView,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CategoryMatchingView},
  template: '<category-matching-view  :matching-progress="matchingProgress" />',
});

export const Default: any = Template.bind({});
Default.args = {
  data: {
    matchingProgress: {total: 42, matched: 32},
  },
};
// TODO
