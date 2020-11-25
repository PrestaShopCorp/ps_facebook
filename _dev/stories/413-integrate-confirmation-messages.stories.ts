import SuccessAlert from '../src/components/features/success-alert.vue';

/* global window */

export default {
  title: 'Integrate/Confirmation messages',
  component: SuccessAlert,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {SuccessAlert},
  template: '<div><success-alert v-for="(feature, index) in newFeatures" :key="index" :name="feature" :shop-url="shopUrl" :show="true" /></div>',
});

// Enabled on FB side
export const AllFeatures: any = Template.bind({});
AllFeatures.args = {
  newFeatures: [
    'messenger_chat',
    'page_cta',
    'ig_shopping',
    'page_shop',
  ],
  shopUrl: '#',
};

