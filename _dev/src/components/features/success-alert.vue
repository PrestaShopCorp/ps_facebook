<template>
  <b-alert
    variant="success"
    dismissible
    :show="show"
  >
    {{ $t('integrate.success.featureEnabled', [$t(`integrate.features.${name}.name`)]) }}
    <b-link v-if="link" :src="link.src" class="font-weight-bold">
      {{ link.text }}
    </b-link>
  </b-alert>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {
  BAlert,
  BLink,
} from 'bootstrap-vue';

export default defineComponent({
  name: 'SuccessAlert',
  components: {
    BAlert,
    BLink
  },
  props: {
    name: {
      type: String,
      required: true,
    },
    shopUrl: {
      type: String,
      required: true,
    },
    show: {
      type: Boolean,
      required: false,
      default: () => false
    }
  },
  computed: {
    link() {
      if ('messenger_chat' === this.name) {
        return {
          text: this.$i18n.t('integrate.success.shopLink'),
          src: this.shopUrl
        };
      }
      return null;
    }
  },
});
</script>

<style lang="scss" scoped>
.flex-grow-1 {
  flex-grow:1
}
.alert {
  padding-left: 3.8rem !important;
  padding-right: 0.5rem !important;
}
</style>
