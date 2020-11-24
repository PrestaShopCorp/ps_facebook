import Messages from '../src/components/configuration/messages.vue';

export default {
  title: 'Configuration/Messages component',
  component: Messages,
};

const props = ':showOnboardSucceeded="showOnboardSucceeded" :showSyncCatalogAdvice="showSyncCatalogAdvice" :categoryMatchingStarted="categoryMatchingStarted" :productSyncStarted="productSyncStarted" :adCampaignStarted="adCampaignStarted"';
const events = '@onCategoryMatchingClick="onCategoryMatchingClick" @onSyncCatalogClick="onSyncCatalogClick" @onAdCampaignClick="onAdCampaignClick"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Messages },
  template: `<messages ${props} ${events} />`,
});
export const AllShown:any = Template.bind({});
AllShown.args = {
  showOnboardSucceeded: true,
  showSyncCatalogAdvice: true,
  categoryMatchingStarted: true,
  productSyncStarted: false,
  adCampaignStarted: false,
};
export const NothingShown:any = Template.bind({});
NothingShown.args = {};
