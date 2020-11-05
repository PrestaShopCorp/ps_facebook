<template>
  <li :class="switchActivated ? null : 'disabled'">
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
              <img
                class="mr-1"
                :src="require(`@/assets/${name}.png`)"
                width="40"
              >
              {{ $t(`integrate.features.${name}.name`) }}
              <b-icon-info-circle
                :id="`tooltip-circle-${name}`"
                class="iconInfo ml-2"
                variant="primary"
              />
              <b-tooltip
                :target="`tooltip-circle-${name}`"
                container="#integrate"
                triggers="hover"
                placement="right"
              >
                {{ $t(`integrate.features.${name}.toolTip`) }}
              </b-tooltip>
            </h3>
          </div>
          <div>
            <span class="d-none d-sm-inline">
              {{
                $t(switchActivated ?
                  'configuration.app.activated' :
                  'configuration.app.disabled')
              }}
            </span>
            <div
              class="switch-input switch-input-lg ml-1"
              :class="switchActivated ? '-checked' : null"
              @click="switchClick"
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
        <div class="d-flex">
          <div class="flex-grow-1" />
          <div>
            <a
              class="align-self-center"
              :href="manageRoute"
              target="_blank"
            >
              <b-button
                variant="outline-secondary"
                class="ml-4 align-self-center"
              >
                {{ $t('integrate.buttons.manage') }}
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
  BIconInfoCircle,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'EnabledFeature',
  components: {
    BCard,
    BCardBody,
    BButton,
    BTooltip,
    BIconInfoCircle,
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
      type: String,
      required: false,
      default: () => global.facebookManageFeaturesRoute,
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
        this.isLoading = true;
        if (!this.switchActivated) {
          this.updateFeatureState();
        }
      }
    },
    updateFeatureState() {
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
      h3, p {
        color: grey !important;
      }
      h3 {
        img {
          filter: grayscale(100);
        }
      }
      .switch-input {
        background-color: #c05c67 !important;
      }
      .switch-input::after {
        color: #c05c67 !important;
      }
    }
    .flex-grow-1 {
      flex-grow:1
    }
  }
</style>
