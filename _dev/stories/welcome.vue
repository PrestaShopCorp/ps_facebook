<template>
  <div class="main">
    <h1>Welcome to STORYBOOK</h1>
    <p>
      This is a UI component dev environment for your app.
    </p>
  </div>
</template>

<script lang="ts">
import {
  defineComponent,
} from "@vue/composition-api";

export default defineComponent({
  name: 'Welcome',
});
</script>

<style lang="scss" scoped>
.main {
  padding: 15px;
  line-height: 1.4;
  font-family: Open Sans, Helvetica, Arial, freesans, sans-serif;
  background-color: '#ffffff';
}
</style>
