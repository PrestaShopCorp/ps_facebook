import Stepper from '../src/components/configuration/stepper.vue';

export default {
  title: 'Configuration/Stepper component',
  component: Stepper,
};

const props = ':psAccountsOnboarded="psAccountsOnboarded" :psFacebookOnboarded="psFacebookOnboarded" :categoryMatchingStarted="categoryMatchingStarted" :productSyncStarted="productSyncStarted" :adCampaignStarted="adCampaignStarted"';
const events = '@onSyncCatalogClick="onSyncCatalogClick" @onCategoryMatchingClick="onCategoryMatchingClick" @onAdCampaignClick="onAdCampaignClick"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Stepper },
  template: `<stepper ${props} ${events} />`,
});
export const NothingDone:any = Template.bind({});
NothingDone.args = {
  psAccountsOnboarded: false,
  psFacebookOnboarded: false,
  categoryMatchingStarted: false,
  productSyncStarted: false,
  adCampaignStarted: false,
};

export const AccountsOnboardingDone:any = Template.bind({});
AccountsOnboardingDone.args = {
  psAccountsOnboarded: true,
  psFacebookOnboarded: false,
  categoryMatchingStarted: false,
  productSyncStarted: false,
  adCampaignStarted: false,
};

export const FacebookOnboardingDone:any = Template.bind({});
FacebookOnboardingDone.args = {
  psAccountsOnboarded: true,
  psFacebookOnboarded: true,
  categoryMatchingStarted: false,
  productSyncStarted: false,
  adCampaignStarted: false,
};

export const CategoryMatchingDone:any = Template.bind({});
CategoryMatchingDone.args = {
  psAccountsOnboarded: true,
  psFacebookOnboarded: true,
  categoryMatchingStarted: true,
  productSyncStarted: false,
  adCampaignStarted: false,
};

export const AllDone:any = Template.bind({});
AllDone.args = {
  psAccountsOnboarded: true,
  psFacebookOnboarded: true,
  categoryMatchingStarted: true,
  productSyncStarted: true,
  adCampaignStarted: true,
};
