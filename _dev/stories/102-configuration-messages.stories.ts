import Messages from '../src/components/configuration/messages.vue';

export default {
  title: 'Configuration/Messages component',
  component: Messages,
};

const props = ':showOnboardSucceeded="showOnboardSucceeded"';
const events = '@onCategoryMatchingClick="onCategoryMatchingClick" @onSyncCatalogClick="onSyncCatalogClick" @onAdCampaignClick="onAdCampaignClick"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Messages },
  template: `<messages ${props} ${events} />`,
});

export const AllShown:any = Template.bind({});
AllShown.args = {
  showOnboardSucceeded: true,
  alertSettings: {},
};

export const NothingShown:any = Template.bind({});
NothingShown.args = {};
