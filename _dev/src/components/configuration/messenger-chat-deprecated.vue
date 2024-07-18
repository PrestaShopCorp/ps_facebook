<template>
  <b-alert
    :show="canDisplay"
    variant="warning"
  >
    <div
      class="d-flex flex-column flex-md-row justify-content-between"
    >
      <p class="mb-0">
        <strong class="ps_gs-fz-16">
          {{ $t('configuration.disableMessenger.title') }}
        </strong>
        <br>
        <span>
          {{ $t('configuration.disableMessenger.description') }}
        </span>
      </p>
      <div
        class="d-md-flex text-center align-items-end mt-2"
      >
        <b-button
          class="mx-1 mt-3 mt-md-0 mr-md-1 text-nowrap ml-auto btn-outline-secondary"
          variant="link"
          @click="redirectToFb()"
        >
          {{ $t('configuration.disableMessenger.more') }}
        </b-button>
        <b-button
          class="mx-1 mt-3 mt-md-0 mr-md-1 text-nowrap ml-auto"
          variant="outline-primary"
          @click="disableMessenger()"
        >
          <span class="d-inline-flex">
            {{ $t('configuration.disableMessenger.btn') }}
            <span
              v-if="isLoading"
              class="spinner ml-1"
            />
          </span>
        </b-button>
      </div>
    </div>
  </b-alert>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'MessengerChatDeprecated',
  computed: {
  },
  data() {
    return {
      isLoading: false,
      canDisplay: true,
    };
  },
  methods: {
    disableMessenger() {
      this.isLoading = true;
      fetch(window.psFacebookDisableMessengerChat)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          this.canDisplay = false;
          return res.json();
        }).catch((error) => {
          console.error(error);
        }).finally(() => {
          this.isLoading = false;
        });
    },
    redirectToFb() {
      window.open('https://developers.facebook.com/docs/messenger-platform/discovery/facebook-chat-plugin/', '_blank');
    },
  },
});
