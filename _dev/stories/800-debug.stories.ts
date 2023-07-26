import DebugPage from '@/views/debug-page.vue';

export default {
  title: 'Others',
  component: DebugPage,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {DebugPage},
  template: '<debug-page :locale="locale" />',
});

export const Debug: any = Template.bind({});
Debug.args = {
};
