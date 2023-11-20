import MessagesContainer from '../src/components/configuration/messages-container.vue';

export default {
  title: 'Configuration/Messages component',
  component: MessagesContainer,
};

const props = ':showOnboardSucceeded="showOnboardSucceeded"';

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { MessagesContainer },
  template: `<messages-container ${props} />`,
});

export const AllShown:any = Template.bind({});
AllShown.args = {
  showOnboardSucceeded: true,
  alertSettings: {},
};

export const NothingShown:any = Template.bind({});
NothingShown.args = {};
