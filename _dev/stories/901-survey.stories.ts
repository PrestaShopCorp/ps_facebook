import CardSurvey from '../src/components/survey/card-survey.vue';

export default {
  title: 'Others/Survey',
  component: CardSurvey,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {CardSurvey},
  template: '<card-survey :locale="locale" />',
});

export const Default: any = Template.bind({});
Default.args = {
  locale: 'en-US',
};

export const LanguageNotAvailable: any = Template.bind({});
LanguageNotAvailable.args = {
  locale: 'zh-CN',
};
