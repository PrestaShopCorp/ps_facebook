import Messages from '../src/components/configuration/messages.vue';

export default {
  title: 'Configuration/Messages component',
  component: Messages,
};

const props = ':showOnboardSucceeded="showOnboardSucceeded"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Messages },
  template: `<messages ${props} />`,
});

export const AllShown:any = Template.bind({});
AllShown.args = {
  showOnboardSucceeded: true,
  alertSettings: {},
};

export const NothingShown:any = Template.bind({});
NothingShown.args = {};
