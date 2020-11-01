<template>
  <div class="alert medium-alert alert-warning mt-3" role="alert">
    <p class="alert-text">
      {{ warningText }}
    </p>
    <slot>
    </slot>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody} from 'bootstrap-vue';

export default defineComponent({
  name: 'Warning',
  components: {
    BCard,
    BCardBody,
  },
  props: {
    warningText: {
      type: String,
      required: true,
    },
  },
});
</script>

<style lang="scss" scoped>
$dark-orange: #d3a342;
$light-orange: #fce7bb;
.card {
  border: 1px solid $dark-orange;
  .card-body{
    padding: 0 !important;
    border: 1px solid $dark-orange;
    .warning-left{
      background-color: $light-orange;
      color: $dark-orange;
      font-size: 30px;
    }
  }
}
.flex-grow-1 {
  flex-grow:1
}
</style>
