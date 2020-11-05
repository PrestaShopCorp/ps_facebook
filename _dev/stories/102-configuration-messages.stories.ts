import Messages from '../src/components/configuration/messages.vue';

export default {
  title: 'Configuration/Messages component',
  component: Messages,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Messages },
  template: '<messages :showOnboardSucceeded="showOnboardSucceeded" :showSyncCatalogAdvice="showSyncCatalogAdvice" :categoryMatchingStarted="categoryMatchingStarted" :productSyncStarte="productSyncStarted" @onSyncCatalogAdviceClick="onSyncCatalogAdviceClick" />',
});
export const AllShown:any = Template.bind({});
AllShown.args = {
  showOnboardSucceeded: true,
  showSyncCatalogAdvice: true,
  categoryMatchingStarted: true,
  productSyncStarted: false,
};
export const NothingShown:any = Template.bind({});
NothingShown.args = {};
