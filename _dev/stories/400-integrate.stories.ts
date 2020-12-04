import Integrate from '../src/views/integrate.vue';

/* global window */

export default {
  title: 'Integrate/Whole tab',
  component: Integrate,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Integrate},
  template: '<integrate :enabledFeatures="enabledFeatures" :availableFeatures="disabledFeatures" :unavailableFeatures="unavailableFeatures" :manageRoute="manageRoute" />',
});

const manageRoute = {
  default: `https://www.facebook.com/facebook_business_extension?app_id=0&external_business_id=0`,
  page_cta: `https://www.facebook.com/0`
};

export const NoFeaturesEnabled: any = Template.bind({});
NoFeaturesEnabled.args = {
  enabledFeatures: [],
  disabledFeatures: {
    "messenger_chat": {
      enabled: false
    },
    "page_cta": {
      enabled: false
    }
  },
  unavailableFeatures: {
    "ig_shopping": {
      enabled: false
    },
    "page_shop": {
      enabled: false
    }
  },
  manageRoute,
};

export const MessengerEnabled: any = Template.bind({});
MessengerEnabled.args = {
  enabledFeatures: {
    "messenger_chat": {
      enabled: true
    },
  },
  disabledFeatures: {
    "page_cta": {
      enabled: false
    }
  },
  unavailableFeatures: {
    "ig_shopping": {
      enabled: false
    },
    "page_shop": {
      enabled: false
    }
  },
  manageRoute,
};

export const WithCatalogSynchronized: any = Template.bind({});
WithCatalogSynchronized.args = {
  enabledFeatures: {
    "messenger_chat": {
      enabled: true
    },
  },
  disabledFeatures: {
    "page_cta": {
      enabled: false
    },
    "ig_shopping": {
      enabled: false
    },
    "page_shop": {
      enabled: false
    }
  },
  unavailableFeatures: {
  },
  manageRoute,
};

export const AllFeaturesEnabled: any = Template.bind({});
AllFeaturesEnabled.args = {
  enabledFeatures: {
    "messenger_chat": {
      enabled: true
    },
    "page_cta": {
      enabled: true
    },
    "ig_shopping": {
      enabled: true
    },
    "page_shop": {
      enabled: false
    },
  },
  disabledFeatures: {
  },
  unavailableFeatures: {
  },
  manageRoute,
};

export const WarningWhenNoFeatures: any = Template.bind({});
WarningWhenNoFeatures.args = {
  enabledFeatures: {
  },
  disabledFeatures: {
  },
  unavailableFeatures: {
  },
  manageRoute,
};
