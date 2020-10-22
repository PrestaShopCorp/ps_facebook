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
  },
});
</script>

<style lang="scss" scoped>
  .flex-grow-1 {
    flex-grow:1
  }
</style>
