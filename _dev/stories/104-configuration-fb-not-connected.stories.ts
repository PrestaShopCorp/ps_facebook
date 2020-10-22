import FacebookNotConnected from '../src/components/configuration/facebook-not-connected.vue';

export default {
  title: 'Configuration/Facebook not connected',
  component: FacebookNotConnected,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { FacebookNotConnected },
  template: '<facebook-not-connected @onFbeOnboardClick="onFbeOnboardClick" />',
});
export const Default:any = Template.bind({});
Default.args = { };
