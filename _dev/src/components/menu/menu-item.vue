<template>
  <li
    class="nav-item"
  >
    <router-link
      active-class="active"
      class="nav-link"
      :to="route"
    >
      <slot />
    </router-link>
  </li>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'MenuItem',
  props: {
    route: {
      type: String,
      required: true,
    },
  },
});
</script>
