import AlertSubscribeToContinue from '@/components/monetization/alert-subscribe-to-continue.vue';

export default {
  title: "Configuration/Billing/Alerts",
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: {
    AlertSubscribeToContinue,
  },
  template: `
    <alert-subscribe-to-continue />
  `,
});
export const SubscribeToContinue: any = Template.bind({});
SubscribeToContinue.args = {};
