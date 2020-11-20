<!--**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <b-container class="m-auto p-0">
    <b-card no-body>
      <template v-slot:header>
        <i class="material-icons">help</i>{{ $t("help.title") }}
      </template>

      <b-card-body>
        <div class="d-flex">
          <div class="left-block">
            <div class="module-desc d-flex mb-4">
              <div class="module-img mr-3">
                <img
                  src="@/assets/logo.png"
                  width="75"
                  height="75"
                  alt=""
                >
              </div>
              <div>
                <b>{{ $t("help.allowsYouTo.title") }}</b>
                <ul class="mt-3">
                  <li>{{ $t("help.allowsYouTo.business") }}</li>
                  <li>{{ $t("help.allowsYouTo.account") }}</li>
                  <li>{{ $t("help.allowsYouTo.traffic") }}</li>
                  <li>{{ $t("help.allowsYouTo.inventory") }}</li>
                  <li>{{ $t("help.allowsYouTo.people") }}</li>
                </ul>
              </div>
            </div>
            <div class="faq">
              <h1>{{ $t("faq.title") }}</h1>
              <div class="separator my-3" />
              <template v-if="faq && faq.categories">
                <v-collapse-group
                  class="my-3"
                  v-for="(categorie, index) in faq.categories"
                  :key="index"
                  :only-one-active="true"
                >
                  <h3 class="categorie-title">
                    {{ categorie.title }}
                  </h3>
                  <v-collapse-wrapper
                    :ref="index + '_' + i"
                    v-for="(item, i) in categorie.blocks"
                    :key="i"
                  >
                    <div
                      class="my-3 question"
                      v-collapse-toggle
                    >
                      <a><i class="material-icons">keyboard_arrow_right</i>
                        {{ item.question }}</a>
                    </div>
                    <div
                      class="answer"
                      :class="'a' + i"
                      v-collapse-content
                    >
                      {{ item.answer }}
                    </div>
                  </v-collapse-wrapper>
                </v-collapse-group>
              </template>
              <template v-else>
                <b-alert
                  variant="warning"
                  show
                >
                  <p>{{ $t("faq.noFaq") }}</p>
                </b-alert>
              </template>
            </div>
          </div>
          <div class="right-block">
            <div class="doc">
              <b class="text-muted">{{ $t("help.help.needHelp") }}</b>
              <br>
              <b-button
                class="mt-3"
                variant="primary"
                @click="getDocumentation()"
              >
                {{ $t("help.help.downloadPdf") }}
              </b-button>
            </div>
            <div class="contact mt-4">
              <div>{{ $t("help.help.couldntFindAnyAnswer") }}</div>
              <div class="mt-2">
                <b-button
                  variant="link"
                  @click="contactUs()"
                >
                  {{ $t("help.help.contactUs") }}
                  <i class="material-icons">arrow_right_alt</i>
                </b-button>
              </div>
            </div>
          </div>
        </div>
      </b-card-body>
    </b-card>
  </b-container>
</template>

<script lang="ts">
import {defineComponent} from '@vue/composition-api';

export default defineComponent({
  props: ['faq', 'contactUsLink', 'docLink'],
  methods: {
    contactUs() {
      window.open(this.$props.contactUsLink, '_blank');
    },
    getDocumentation() {
      window.open(this.$props.docLink, '_blank');
    },
  },
});
</script>

<style scoped>
.separator {
  height: 1px;
  opacity: 0.2;
  background: #6b868f;
  border-bottom: 2px solid #6b868f;
}
.left-block {
  flex-grow: 1;
}
.right-block {
  padding: 15px;
  min-width: 350px;
  text-align: center;
}
.doc {
  padding: 20px;
  background-color: #f7f7f7;
}
.answer {
  margin: 0px 15px 10px 15px;
  padding: 15px;
  background-color: #f7f7f7;
}
.question {
  cursor: pointer;
}
.icon-expand {
  transform: rotate(90deg);
  transition: all 0.3s;
}
.v-collapse-content {
  display: none;
}
.v-collapse-content-end {
  display: block;
}
</style>
