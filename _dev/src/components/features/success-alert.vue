<template>
  <b-alert
    variant="success"
    dismissible
    show
  >
    {{ $t('integrate.success.featureEnabled', [$t(`integrate.features.${name}.name`)]) }}
    <b-link
      v-if="link"
      :href="link.src"
      class="font-weight-bold"
      target="_blank"
    >
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
    BLink,
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
  },
  computed: {
    link() {
      if (this.name === 'messenger_chat') {
        return {
          text: this.$i18n.t('integrate.success.shopLink'),
          src: this.shopUrl,
        };
      }
      return null;
    },
  },
});
</script>

<style lang="scss" scoped>
.flex-grow-1 {
  flex-grow:1
}
</style>
