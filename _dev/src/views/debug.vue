<!--**
 * 2007-2021 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div
    class="ps-facebook-debug-tab"
  >
    <b-alert
      variant="info"
      show
      class="m-3"
    >
      This tab can be used to modify the configuration of this module without having to
      connect on the database engine or modifying the code.<br>
      Only advanced users should use this tab. <b>Use at your own risk.</b>
    </b-alert>

    <b-card
      no-body
      class="m-3"
    >
      <template v-slot:header>
        Module status
      </template>

      <b-card-body class="row">
        <div class="col-6">
          <p class="h3">
            External Business ID
          </p>
          <pre>{{ $root.psFacebookExternalBusinessId }}</pre>

          <p class="h3">
            Health Check
          </p>
          <pre>{{ healthCheckContent }}</pre>
        </div>
        <div class="col-6">
          <p class="h3">
            Facebook Business Extension data
          </p>
          <pre>{{ contextPsFacebook }}</pre>
        </div>
      </b-card-body>
    </b-card>

    <b-card
      no-body
      class="m-3"
    >
      <template v-slot:header>
        Conversion API data management
      </template>

      <b-card-body>
        <!-- System Access Token -->
        <b-form @submit.stop.prevent>
          <b-form-group>
            <label for="text-system-access-token">System Access Token</label>
            <b-form-input
              v-model="systemAccessToken"
              type="text"
              id="text-system-access-token"
              aria-describedby="system-access-token-help-block"
              :disabled="isLoading"
            />
            <b-form-text id="system-access-token-help-block">
              You can replace the System Access Token stored in the database if the one
              generated from the FBE user access token does not work.<br>
              They can be generated in
              <b-link
                :href="`https://business.facebook.com/events_manager2/list/pixel/${pixelID}/setting`"
              >
                the Event Manager settings
              </b-link>.
            </b-form-text>
          </b-form-group>
          <b-form-group>
            <b-button
              type="submit"
              variant="primary"
              @click="updateSystemToken"
            >
              Replace System Access Token
            </b-button>
          </b-form-group>
        </b-form>

        <!-- Event Test Code -->
        <b-form @submit.stop.prevent>
          <b-form-group inline>
            <label for="inline-event-test-code">Test Event Code</label>
            <b-form-input
              id="inline-event-test-code"
              placeholder="New Test Code"
              aria-describedby="event-test-code-help-block"
              v-model="testEventCode"
              :disabled="isLoading"
            />
            <b-form-text id="event-test-code-help-block">
              The test events tool is used to verify the events are properly sent from the shop
              to Facebook (
              <b-link
                href="https://developers.facebook.com/docs/marketing-api/conversions-api/using-the-api?locale=en_US#testEvents"
              >
                Facebook documentation
              </b-link>
              ).<br>The value of the Test Event Code can be found in
              <b-link
                :href="`https://business.facebook.com/events_manager2/list/pixel/${pixelID}/test_events`"
              >
                the Event Manager test tab
              </b-link>.
            </b-form-text>
          </b-form-group>
          <b-form-group>
            <b-button
              type="submit"
              variant="primary"
              @click="updateTestEventCode"
            >
              Set Test Event Code
            </b-button>
            <b-button
              type="submit"
              variant="danger"
              @click="deleteTestEventCode"
            >
              Remove
            </b-button>
          </b-form-group>
        </b-form>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  name: 'Debug',
  components: {
  },
  props: {
  },
  data() {
    return {
      systemAccessToken: null,
      testEventCode: null,
      isLoading: false,
      healthCheckContent: {},
    };
  },
  computed: {
    pixelID() {
      if (this.$root.contextPsFacebook) {
        return this.$root.contextPsFacebook.pixel.id;
      }
      return null;
    },
    contextPsFacebook() {
      return JSON.stringify(this.$root.contextPsFacebook, null, 2) || 'Loading...';
    },
  },
  methods: {
    updateSystemToken() {
      this.sendPostRequest({
        system_access_token: this.systemAccessToken,
      });
    },
    updateTestEventCode() {
      this.sendPostRequest({
        test_event: this.testEventCode,
      });
    },
    deleteTestEventCode() {
      this.sendPostRequest({
        drop_test_event: 1,
      });
    },
    sendPostRequest(data) {
      this.isLoading = true;
      fetch(global.psFacebookUpdateConversionApiData, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
        body: JSON.stringify(data),
      }).then((res) => {
        this.isLoading = false;
        if (!res.ok) {
          throw new Error(res.statusText || res.status);
        }
        return res.json();
      }).then((res) => {
        if (res.success === false) {
          throw new Error('failed to update feature');
        }
      }).catch((error) => {
        this.isLoading = false;
        console.error(error);
      });
    },
    callHeathCheck() {
      fetch(global.psFacebookHealthCheckRoute)
        .then((res) => {
          if (!res.ok) {
            throw new Error(res.statusText || res.status);
          }
          return res.json();
        }).then((res) => {
          this.healthCheckContent = res;
        }).catch((error) => {
          console.error(error);
        });
    },
  },
  created() {
    this.callHeathCheck();
  },
});
</script>

<style lang="scss">
  .ps-facebook-debug-tab {
    div.card {
      border: none !important;
      border-radius: 3px;
    }
  }
</style>
