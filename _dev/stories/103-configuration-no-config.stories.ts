import NoConfig from '../src/components/configuration/no-config.vue';

export default {
  title: 'Configuration/NoConfig component',
  component: NoConfig,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { NoConfig },
  template: '<no-config />',
});
export const Default:any = Template.bind({});
Default.args = { };
