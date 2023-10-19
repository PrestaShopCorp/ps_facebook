<template>
  <div class="app pb-2 px-2">
    <div class="d-flex">
      <img
        v-if="!!logo"
        :src="logo"
        alt="app logo"
        class="logo mr-3 my-1"
      >
      <div>
        <div class="font-weight-500 d-flex ps_gs-fz-16 mb-2">
          {{ appType }}
          <tooltip
            v-if="!!tooltip"
            :text="tooltip"
          />
        </div>
        <div class="font-weight-500 ps_gs-fz-14 text-truncate">
          {{ appName }}

          <span
            v-if="!!url"
            class="url"
          >
            &nbsp;/&nbsp;
            <b-link
              :href="url"
              target="_blank"
              @click="onStats"
            >
              {{ $t('configuration.app.viewStats') }}
            </b-link>
          </span>
        </div>

        <div v-if="displayWarning">
          <warning :warning-text="$t('configuration.app.informationCannotBeDisplayedWarning')" />
        </div>
        <div v-else>
          <div
            v-if="!!email"
            class="text-truncate"
          >
            {{ email }}
          </div>
          <div
            v-if="!!appId"
            class="text-truncate"
          >
            {{ appId }}
          </div>
          <div
            v-if="null !== likes"
          >
            {{ $tc('configuration.app.nbLikes', likes, [likes]) }}
          </div>
          <div
            v-if="!!createdAt"
          >
            {{ $t('configuration.app.createdAt') }}
            {{ new Date(createdAt).toLocaleDateString(undefined, { dateStyle: 'medium' }) }}
          </div>
          <div
            v-if="!!lastActive"
          >
            {{ $t('configuration.app.lastActive') }}
            {{ new Date(lastActive).toLocaleDateString(undefined, { dateStyle: 'medium' }) }}
            {{ new Date(lastActive).toLocaleTimeString(undefined) }}
          </div>
          <div
            v-if="activationSwitch"
          >
            {{ $t('configuration.app.status') }}
            <b-form-checkbox
              switch
              size="lg"
              class="ml-1 ps_gs-switch"
              v-model="switchActivated"
              :disabled="frozenSwitch"
              inline
            >
              {{ statusText }}
            </b-form-checkbox>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {BFormCheckbox, BLink} from 'bootstrap-vue';
import Tooltip from '@/components/help/tooltip.vue';
import Warning from '@/components/warning/warning.vue';

export default defineComponent({
  name: 'FacebookApp',
  components: {
    BFormCheckbox,
    BLink,
    Tooltip,
    Warning,
  },
  props: {
    appType: {
      type: String,
      required: true,
    },
    appName: {
      type: String,
      default: null,
    },
    email: {
      type: String,
      required: false,
      default: null,
    },
    appId: {
      type: String,
      required: false,
      default: null,
    },
    likes: {
      type: Number,
      required: false,
      default: null,
    },
    url: {
      type: String,
      required: false,
      default: null,
    },
    createdAt: {
      type: String,
      required: false,
      default: null,
    },
    lastActive: {
      type: String,
      required: false,
      default: null,
    },
    activationSwitch: {
      type: Boolean,
      required: false,
      default: null,
    },
    frozenSwitch: {
      type: Boolean,
      required: false,
      default: false,
    },
    tooltip: {
      type: String,
      required: false,
      default: null,
    },
    logo: {
      type: String,
      required: false,
      default: null,
    },
    displayWarning: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      switchActivated: this.activationSwitch as boolean,
    };
  },
  computed: {
    statusText() {
      if (this.frozenSwitch) {
        return this.$t('configuration.app.moduleDisabled');
      }
      if (!this.switchActivated) {
        return this.$t('configuration.app.disabled');
      }
      return this.$t('configuration.app.activated');
    },
  },
  methods: {
    switchClick() {
      if (this.frozenSwitch) {
        return;
      }
      this.switchActivated = !this.switchActivated;
    },
    onStats() {
      this.$segment.track('Click on view stat CTA', {
        module: 'ps_facebook',
      });
    },
  },
  watch: {
    activationSwitch(newValue: boolean) {
      this.switchActivated = newValue;
    },
    switchActivated(newValue: boolean) {
      this.$emit('onActivation', newValue);
      this.$segment.track('Click on pixel switch CTA', {
        module: 'ps_facebook',
      });
      this.$segment.track(`Feature Pixel ${newValue ? 'enabled' : 'disabled'}`, {
        module: 'ps_facebook',
      });
    },
  },
});
</script>
