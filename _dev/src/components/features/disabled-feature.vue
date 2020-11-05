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
          <div class="description align-self-center flex-grow-1 pl-3 pr-2">
            <h3>
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
import {
  BCard,
  BCardBody,
  BButton,
  BIconInfoCircle,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'DisabledFeature',
  components: {
    BCard,
    BCardBody,
    BButton,
    BIconInfoCircle,
  },
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
