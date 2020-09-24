import Configuration from '../src/views/configuration.vue';

export default {
  title: 'Configuration/Whole tab',
  component: Configuration,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Configuration },
  template: '<configuration />',
});
export const Default: any = Template.bind({});
Default.args = { };
