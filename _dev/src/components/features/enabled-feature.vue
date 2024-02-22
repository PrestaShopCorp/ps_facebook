<template>
  <li
    class="ps_fb_featurecard"
    :class="switchActivated ? null : 'disabled'"
  >
    <b-card no-body>
      <b-card-body
        class="p-3"
      >
        <div class="feature-header d-flex">
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <span class="h1">
              <img
                class="mr-1 logo"
                :src="imageUrl"
              >
              {{ $t(`integrate.features.${name}.name`) }}
            </span>
            <tooltip-stack :text="$t(`integrate.features.${name}.description`)" />
          </div>
          <div
            class="align-self-center"
            v-if="allowDisplayOfSwitch"
          >
            <span class="d-none d-sm-inline">
              {{ statusText }}
            </span>
            <div
              class="switch-input switch-input-lg ml-1"
              :class="[
                switchActivated && !frozenSwitch ? '-checked' : null,
                isLoading || frozenSwitch ? 'disabled' : null
              ]"
              @click="switchClick()"
            >
              <input
                class="switch-input-lg"
                type="checkbox"
                :checked="switchActivated && !frozenSwitch"
              >
            </div>
          </div>
        </div>
        <div
          class="d-flex"
          v-if="switchActivated"
        >
          <div class="flex-grow-1" />
          <div>
            <b-button
              v-if="name === 'messenger_chat'"
              variant="primary"
              class="ml-4 align-self-center"
              :href="manageRoute.view_message_url"
              target="_blank"
            >
              {{ $t(`integrate.features.${name}.checkMessages`) }}
            </b-button>
            <b-button
              variant="outline-secondary"
              class="ml-4 align-self-center"
              @click="onManageClick(name)"
              :href="manageRoute[name] || manageRoute.default"
              target="_blank"
            >
              {{ $t(`integrate.features.${name}.editButton`) }}
            </b-button>
          </div>
        </div>
      </b-card-body>
    </b-card>
    <ps-modal
      :id="`modal_${name}`"
      :ref="`modal_${name}`"
      :title="$t('integrate.warning.disableFeatureModalHeader')"
      @ok="updateFeatureState"
      @cancel="isLoading = false"
      ok-only
    >
      {{ $t('integrate.warning.disableFeatureModalText') }}
      <template slot="modal-ok">
        {{ $t('cta.modalConfirm') }}
      </template>
    </ps-modal>
  </li>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import PsModal from '@/components/commons/ps-modal.vue';
import TooltipStack from '@/components/help/tooltip-stack.vue';

export default defineComponent({
  name: 'EnabledFeature',
  components: {
    PsModal,
    TooltipStack,
  },
  mixins: [],
  props: {
    name: {
      type: String,
      required: true,
      default: () => '',
    },
    active: {
      type: Boolean,
      required: true,
      default: false,
    },
    frozenSwitch: {
      type: Boolean,
      required: false,
      default: false,
    },
    allowDisplayOfSwitch: {
      type: Boolean,
      required: false,
      default: true,
    },
    loading: {
      type: Boolean,
      required: false,
      default: false,
    },
    updateFeatureRoute: {
      type: String,
      required: false,
      default: window.psFacebookUpdateFeatureRoute,
    },
    manageRoute: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      switchActivated: this.active || !this.allowDisplayOfSwitch,
      isLoading: this.loading,
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
    imageUrl(): string {
      return new URL(`/src/assets/${this.name}.svg`, import.meta.url).href;
    },
  },
  methods: {
    switchClick() {
      if (this.frozenSwitch) {
        return;
      }
      if (!this.isLoading) {
        if (!this.switchActivated) {
          this.updateFeatureState();
        } else {
          this.$bvModal.show(
            this.$refs[`modal_${this.name}`].$refs.modal.id,
          );
          this.$segment.track('[FBK] Sales Channels - Disable modal displayed', {
            module: 'ps_facebook',
          });
        }
      }
    },
    onManageClick(name) {
      this.$segment.track(`[FBK] Click on "manage" ${name}`, {
        module: 'ps_facebook',
      });
    },
    updateFeatureState() {
      this.isLoading = true;
      fetch(this.updateFeatureRoute, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify({featureName: this.name, enabled: !this.switchActivated}),
      }).then((res) => {
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        this.isLoading = false;
        if (res.success === false) {
          throw new Error('failed to update feature');
        } else {
          this.switchActivated = !this.switchActivated;
          this.$emit('onToggleSwitch', this.name, this.switchActivated);
        }

        if (this.switchActivated) {
          this.$store.dispatch('app/FIX_UNREGISTERED_HOOKS');
        }
      }).catch((error) => {
        console.error(error);
        this.error = 'integrate.error.failedToUpdateFeature';
      });
    },
  },
  watch: {
    active(newValue) {
      this.switchActivated = newValue;
    },
  },
});
</script>

<style lang="scss" scoped>
  li {
    &.disabled{
      .h1, p {
        color: grey !important;
      }
      .h1 {
        img {
          filter: grayscale(100);
        }
      }
      .switch-input {
        background-color: #c05c67 !important;
        &.disabled {
          background: #eee !important;
        }
      }
      .switch-input::after {
        color: #c05c67 !important;
      }
    }
    .card {
      border: none !important;
      border-radius: 3px;
    }

    .switch-input {
      &.disabled {
        background: #eee !important;
      }
    }
  }
</style>
