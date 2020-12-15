<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="require(`@/assets/${name}.png`)"
            :alt="$t(`integrate.features.${name}.name`)"
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
          <a
            class="align-self-center"
            @click="onSales(name)"
            :href="manageRoute[name] || manageRoute.default"
            target="_blank"
          >
            <b-button
              variant="outline-primary"
              class="ml-4 align-self-center"
            >
              {{ $t(`integrate.features.${name}.addButton`) }}
            </b-button>
          </a>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {
  BCard,
  BCardBody,
  BButton,
} from 'bootstrap-vue';
import Tooltip from '../help/tooltip.vue';

export default defineComponent({
  name: 'DisabledFeature',
  components: {
    BCard,
    BCardBody,
    BButton,
    Tooltip,
  },
  mixins: [],
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
    manageRoute: {
      type: Object,
      required: true,
    },
  },
  methods: {
    onSales(name) {
      this.$segment.track(`Add CTA - ${name}`, {
        module: 'ps_facebook',
      });
    },
  },
});
</script>

<style lang="scss" scoped>
  .logo {
    float: left;
    display: block;
    width: 80px;
    height: 80px;
  }

  .description {
    display: table-cell;
  }

  .flex-grow-1 {
    flex-grow:1
  }
</style>
