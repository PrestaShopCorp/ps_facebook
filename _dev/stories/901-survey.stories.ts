import Survey from '../src/components/survey/survey.vue';

export default {
  title: 'Others/Survey',
  component: Survey,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Survey},
  template: '<survey :locale="locale" />',
});

export const Default: any = Template.bind({});
Default.args = {
  locale: 'en-US',
};

export const LanguageNotAvailable: any = Template.bind({});
LanguageNotAvailable.args = {
  locale: 'zh-CN',
};
