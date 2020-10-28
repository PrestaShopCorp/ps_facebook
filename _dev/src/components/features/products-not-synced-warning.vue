<template>
  <div class="p-3">
    <b-card>
      <div class="d-flex">
        <div class="warning-left">
          <i class="material-icons align-self-center m-3">error_outline</i>
        </div>
        <div class="flex-grow-1 warning-right">
          <p class="m-2">
            {{ $t('integrate.warning.productsNotSynced') }}
          </p>
          <b-button
            variant="primary"
            class="m-2"
          >
            {{ $t('integrate.buttons.syncProducts') }}
          </b-button>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import {BCard, BCardBody, BButton} from 'bootstrap-vue';

export default defineComponent({
  name: 'ProductsNotSyncedWarning',
  components: {
    BCard,
    BCardBody,
    BButton,
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
