import cloneDeep from 'lodash.clonedeep';
import {State as OnboardingState, state as defaultOnboardingState} from '@/store/modules/onboarding/state';
import {stateOnboarded} from "@/../.storybook/mock/onboarding";
import Integrate from '@/views/integrate-tab.vue';

export default {
  title: 'Integrate/Whole tab',
  component: Integrate,
};

const managementRoutes = {
  default: `https://www.facebook.com/facebook_business_extension?app_id=XXX&external_business_id=XXX`,
  messenger_chat: `https://business.facebook.com/latest/inbox/settings/chat_plugin?asset_id=XXX`,
  page_cta: `https://www.facebook.com/XXX`,
  view_message_url: `https://business.facebook.com/latest/inbox/all?asset_id=XXX`,
};

const Template = (args: any, {argTypes}: any) => ({
  props: Object.keys(argTypes),
  components: {Integrate},
  template: '<integrate :enabledFeatures="enabledFeatures" :availableFeatures="disabledFeatures" :unavailableFeatures="unavailableFeatures" :manageRoute="manageRoute" />',
  beforeMount: function(this: any) {
    (this.$store.state.onboarding as OnboardingState) = cloneDeep(stateOnboarded);
  },
});

export const NoFeaturesEnabled: any = Template.bind({});
NoFeaturesEnabled.args = {
  enabledFeatures: {},
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
  manageRoute: managementRoutes,
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
  manageRoute: managementRoutes,
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
  manageRoute: managementRoutes,
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
  manageRoute: managementRoutes,
};

export const WarningWhenNoFeatures: any = Template.bind({});
WarningWhenNoFeatures.args = {
  enabledFeatures: {
  },
  disabledFeatures: {
  },
  unavailableFeatures: {
  },
  manageRoute: managementRoutes,
};
