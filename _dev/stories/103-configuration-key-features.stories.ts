import KeyFeatures from '@/components/configuration/key-features.vue';

export default {
  title: 'Configuration',
  component: KeyFeatures,
};

const Template = (args: any, { argTypes }: any) => ({
  props: Object.keys(argTypes),
  components: { KeyFeatures },
  template: '<key-features />',
});
export const KeyFeaturesCard: any = Template.bind({});
KeyFeaturesCard.args = { };
