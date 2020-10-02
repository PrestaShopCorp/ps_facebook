import Introduction from '../src/components/configuration/introduction.vue';

export default {
  title: 'Configuration/Introduction component',
  component: Introduction,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { Introduction },
  template: '<introduction @onHide="onHide" />',
});
export const Default: any = Template.bind({});
Default.args = { };
