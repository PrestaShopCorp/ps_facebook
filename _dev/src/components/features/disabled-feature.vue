<template>
  <li>
    <b-card no-body>
      <b-card-body>
        <div class="d-flex">
          <img
            class="mr-2 align-self-center logo d-none d-md-block"
            :src="require(`../../assets/${name}.png`)"
            width="80"
          >
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
              {{ $t(`integrate.features.${name}.name`) }}
            </h3>
            <p class="small-text text-muted">
              {{ $t(`integrate.features.${name}.description`) }}
            </p>
          </div>
          <a
            class="align-self-center"
            :href="manageRoute"
            target="_blank"
          >
            <b-button
              variant="outline-primary"
              class="ml-4 align-self-center"
            >
              {{ $t('integrate.buttons.add') }}
            </b-button>
          </a>
        </div>
      </b-card-body>
    </b-card>
  </li>
</template>

<script>
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  name: 'DisabledFeature',
  mixins: [],
  props: {
    name: {
      type: String,
      required: false,
      default: () => '',
    },
    manageRoute: {
      type: String,
      required: false,
      default: () => global.facebookManageFeaturesRoute,
    },
  },
});
</script>

<style lang="scss" scoped>
  .logo {
    float: left;
    display: block;
  }

  .description {
    display: table-cell;
  }

  .flex-grow-1 {
    flex-grow:1
  }
</style>
