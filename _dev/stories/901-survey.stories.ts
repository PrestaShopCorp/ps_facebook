import Survey from '../src/components/survey/survey.vue';

export default {
  title: 'Others/Survey',
  component: Survey,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Survey},
  template: '<survey />',
});

export const Default: any = Template.bind({});
Default.args = {};
