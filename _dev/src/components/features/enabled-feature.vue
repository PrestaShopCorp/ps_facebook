<template>
  <li>
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
            >
              <input
                class="switch-input-lg"
                type="checkbox"
                :checked="switchActivated"
              >
            </div>
          </div>
        </div>
        <div class="d-flex" />
      </b-card-body>
    </b-card>
    <b-modal
      v-model="modalShow"
      :id="`modal_${name}`"
      @ok="handleOk"
    >
      Hello From My Modal!
    </b-modal>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody, BModal} from 'bootstrap-vue';

export default defineComponent({
  name: 'EnabledFeature',
  components: {
    BCard,
    BCardBody,
    BModal,
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
  },
  data() {
    return {
      switchActivated: this.active,
      isLoading: this.loading,
      modalShow: true,
    };
  },
  methods: {
    switchClick() {
      if (!this.isLoading) {
        this.isLoading = true;
        this.updateFeatureState();
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
    handleOk() {
      console.log('ok');
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
  .flex-grow-1 {
    flex-grow:1
  }
</style>
