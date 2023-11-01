import CardBillingConnected from '@/components/configuration/card-billing-connected.vue';
import {runningSubscription, trialNotRenewedSubscription} from '@/../.storybook/mock/ps-billing';

export default {
  title: 'Configuration/Billing Card',
  component: CardBillingConnected,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { CardBillingConnected },
  template: `
    <card-billing-connected
      :subscription="subscription"
    />
  `,
});
export const SubscriptionRunning: any = Template.bind({});
SubscriptionRunning.args = {
  subscription: runningSubscription,
};

export const SubscriptionEnding: any = Template.bind({});
SubscriptionEnding.args = {
  subscription: trialNotRenewedSubscription,
};
