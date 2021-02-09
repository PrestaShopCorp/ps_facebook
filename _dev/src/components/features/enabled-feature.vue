<template>
  <li :class="switchActivated ? null : 'disabled'">
    <b-card no-body>
      <b-card-body>
        <div class="feature-header d-flex">
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <span class="h1">
              <img
                class="mr-1"
                :src="require(`@/assets/${name}.png`)"
                width="40"
              >
              {{ $t(`integrate.features.${name}.name`) }}
            </span>
            <tooltip :text="$t(`integrate.features.${name}.description`)" />
          </div>
          <div
            class="align-self-center"
            v-if="allowDisplayOfSwitch"
          >
            <span class="d-none d-sm-inline">
              {{
                $t(switchActivated ?
                  'configuration.app.activated' :
                  'configuration.app.disabled')
              }}
            </span>
            <div
              class="switch-input switch-input-lg ml-1"
              :class="[switchActivated ? '-checked' : null, isLoading ? 'disabled' : null]"
              @click="switchClick()"
              data-toggle="modal"
              :data-target="switchActivated ? `#modal_${name}` : null"
            >
              <input
                class="switch-input-lg"
                type="checkbox"
                :checked="switchActivated"
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
            <a
              class="align-self-center"
              @click="onManageClick(name)"
              :href="manageRoute[name] || manageRoute.default"
              target="_blank"
            >
              <b-button
                variant="outline-secondary"
                class="ml-4 align-self-center"
              >
                {{ $t(`integrate.features.${name}.editButton`) }}
              </b-button>
            </a>
          </div>
        </div>
      </b-card-body>
    </b-card>
    <div
      :id="`modal_${name}`"
      class="modal"
    >
      <div
        class="modal-dialog"
        role="document"
      >
        <div class="modal-content tw-rounded-none">
          <div class="modal-header">
            <slot name="header">
              <div class="tw-flex tw-items-center">
                <h5 class="modal-title tw-pl-3">
                  {{ $t('integrate.warning.disableFeatureModalHeader') }}
                </h5>
              </div>
            </slot>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="isLoading = false"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            {{ $t('integrate.warning.disableFeatureModalText') }}
          </div>
          <div class="modal-footer">
            <b-button
              variant="primary"
              target="_blank"
              data-dismiss="modal"
              @click="updateFeatureState"
            >
              {{ $t('integrate.buttons.modalConfirm') }}
            </b-button>
          </div>
        </div>
      </div>
    </div>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {
  BCard,
  BCardBody,
  BButton,
  BTooltip,
} from 'bootstrap-vue';
import Tooltip from '../help/tooltip.vue';

export default defineComponent({
  name: 'EnabledFeature',
  components: {
    BCard,
    BCardBody,
    BButton,
    BTooltip,
    Tooltip,
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
      default: global.psFacebookUpdateFeatureRoute,
    },
    manageRoute: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      switchActivated: this.active,
      isLoading: this.loading,
    };
  },
  methods: {
    switchClick() {
      if (!this.isLoading) {
        if (!this.switchActivated) {
          this.updateFeatureState();
        } else {
          this.$segment.track('Sales Channels - Disable modal displayed', {
            module: 'ps_facebook',
          });
        }
      }
    },
    onManageClick(name) {
      this.$segment.track(`Click on "manage" ${name}`, {
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
    .flex-grow-1 {
      flex-grow:1
    }
    .switch-input {
      &.disabled {
        background: #eee !important;
      }
    }
    .feature-header {
      margin-bottom: 1em;
    }
  }
</style>
