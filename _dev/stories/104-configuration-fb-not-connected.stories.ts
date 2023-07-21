import FacebookNotConnected from '../src/components/configuration/facebook-not-connected.vue';

export default {
  title: 'Configuration/Facebook not connected',
  component: FacebookNotConnected,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { FacebookNotConnected },
  template: '<facebook-not-connected @onFbeOnboardClick="onFbeOnboardClick" v-bind="$props" />',
});
export const Inactive:any = Template.bind({});
Inactive.args = {
  active: false,
  canConnect: false,
};

export const WaitingForBusinessId:any = Template.bind({});
WaitingForBusinessId.args = {
  active: true,
  canConnect: false,
};

export const Ready:any = Template.bind({});
Ready.args = {
  active: true,
  canConnect: true,
};
