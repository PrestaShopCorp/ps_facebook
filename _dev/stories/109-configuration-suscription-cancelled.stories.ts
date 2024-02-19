import AlertSubscriptionCancelled from '@/components/configuration/alert-subscription-cancelled.vue';
import {trialEndedSubscription, trialNotRenewedSubscription, makeSubscriptionFromEdition} from '@/../.storybook/mock/ps-billing';

export default {
  title: "Configuration/Billing/Alerts",
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: {
    AlertSubscriptionCancelled,
  },
  template: `
    <alert-subscription-cancelled
      :subscription="billingSubscription"
    />
  `,
});

export const SubscriptionNotRenewed: any = Template.bind({});
SubscriptionNotRenewed.args = {
  billingSubscription: trialNotRenewedSubscription,
};

export const SubscriptionEnded: any = Template.bind({});
SubscriptionEnded.args = {
  billingSubscription: trialEndedSubscription,
};

export const EditionSubscriptionNotRenewed: any = Template.bind({});
EditionSubscriptionNotRenewed.args = {
  billingSubscription: makeSubscriptionFromEdition(trialNotRenewedSubscription),
};

export const EditionSubscriptionEnded: any = Template.bind({});
EditionSubscriptionEnded.args = {
  billingSubscription: makeSubscriptionFromEdition(trialEndedSubscription),
};
