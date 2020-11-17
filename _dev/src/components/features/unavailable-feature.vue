<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="require(`@/assets/${name}_disabled.png`)"
            width="80"
          >
          <div class="description align-self-top flex-grow-1 pl-3 pr-2">
            <h3>
              {{ $t(`integrate.features.${name}.name`) }}
            </h3>
            <p>
              {{ $t(`integrate.features.${name}.description`) }}
            </p>
          </div>
          <b-button
            variant="outline-primary disabled"
            class="ml-4 align-self-center"
          >
            {{ $t(`integrate.features.${name}.addButton`) }}
          </b-button>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody} from 'bootstrap-vue';
import Tooltip from '../help/tooltip.vue';

export default defineComponent({
  name: 'UnavailableFeature',
  components: {
    BCard,
    BCardBody,
    Tooltip,
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
.logo {
  float: left;
  display: block;
  height: 80px;
  width: 80px;
}

.description {
  display: table-cell;
}

.flex-grow-1 {
  flex-grow:1
}
</style>
