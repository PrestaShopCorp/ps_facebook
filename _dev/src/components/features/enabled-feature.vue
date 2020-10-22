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
        <div class="d-flex">
          content
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody} from 'bootstrap-vue';

export default defineComponent({
  name: 'EnabledFeature',
  components: {
    BCard,
    BCardBody,
  },
  mixins: [],
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
    activationSwitch: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
  data() {
    return {
      switchActivated: this.activationSwitch,
    };
  },
  methods: {
    switchClick() {
      this.switchActivated = !this.switchActivated;
      this.$emit('onActivation', this.switchActivated);
    },
  },
  watch: {
    activationSwitch(newValue) {
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
