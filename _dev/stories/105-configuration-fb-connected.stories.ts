import FacebookConnected from '../src/components/configuration/facebook-connected.vue';

export default {
  title: 'Configuration/Facebook connected',
  component: FacebookConnected,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { FacebookConnected },
  template: '<facebook-connected :contextPsFacebook="contextPsFacebook" :startExpanded="startExpanded" />',
});
export const Default:any = Template.bind({});
Default.args = {
  contextPsFacebook: {
    // TODO !0: to define
  }
};
export const DefaultExpanded:any = Template.bind({});
DefaultExpanded.args = {
  contextPsFacebook: {
    // TODO !0: to define
  },
  startExpanded: true,
};
