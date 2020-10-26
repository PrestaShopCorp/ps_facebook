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
  <div class="display-table-matchingFb">
    <b-table-simple :responsive="true">
      <b-thead>
        <b-tr>
          <b-th>Category on your site</b-th>
          <b-th>Facebook category</b-th>
          <b-th>Parent category</b-th>
          <b-th>Facebook subcategory</b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
      /*
      * rajouter une props dans editing row
      */
        <editing-row
          v-for="category in categories"
          v-if="category.show"
          :key="category.shopCategoryId"
          :shopCategoryId="category.shopCategoryId"
          :initialCategoryName="category.categoryName"
          :initialCategoryId="category.categoryId"
          :initialSubcategoryName="category.subcategoryName"
          :initialSubcategoryId="category.subcategoryId"
          :initialPropagation="category.propagation"
          :autocompletionApi="'https://facebook-api.psessentials.net/taxonomy/'"
          :saveMatchingCallback="saveMatchingCallback"
          @rowClicked="getCurrentRow"
        >
          {{ category.shopCategoryName }}
        </editing-row>
      </b-tbody>
    </b-table-simple>
  </div>
</template>

<script>
import {defineComponent} from '@vue/composition-api';
import EditingRow from './editing-row.vue';

export default defineComponent({
  name: 'TableMatching',
  components: {
    EditingRow
  },
  mixins: [],
  props: {
    initialCategories: {
      type: Array,
      required: true
    }
  },
  computed: {
  },
  data() {
    return {
      categories: this.initialCategories,
    };
  },
  methods: {
    saveMatchingCallback() {
      return Promise.resolve(true);
    },
    getCurrentRow(currentShopCategoryID) {
      // Call php for get all subcategories from shopCategoryID
      const subcategory = {
        'shopParentCategoryID': '1',
        'shopCategoryId': '2',
        'shopCategoryName': 'Bird',
        'deploy': null,
        'show': true,
        'categoryName': 'Bird Supplies',
        'categoryId': 2,
        'subcategoryName':'Animals & Pet Supplies > Pet Supplies > Bird Supplies',
        'subcategoryId': 3,
        'propagation': false,
      };

      const currentCategory = this.categories.find(element => element.shopCategoryId == currentShopCategoryID);
      const indexCtg = this.categories.indexOf(currentCategory) + 1;
      currentCategory.deploy = true;
      var nbrFloors = null;

      switch (currentCategory.deploy) {
        case true:
          // replier l'arborescence
          // get all children => show = false
          // deploy = false
          // startWith methods
        break;

        case false:
          // deplier
          // show = true soit pour les enfants direct, et pour les sous enfant dont le pere est a true
          // filter ||
          // recursif
          // get children
        break;

        case undefined:
          // api PHP
          // cast deploy en toString pour le chevron regle css
          // si une reponse deploy = true
          // return (element, array empty => deploy => null)
        break;

        default:
        // nothing
      }
      if (currentCategory.deploy !== null) {
        if (currentCategory.shopParentCategoryIds !== null) {
          nbrFloors = currentCategory.shopParentCategoryIds.split('/');
          // we know the number of children (limit 3 level)
          const findSubCtg = this.categories.find(element => element.shopCategoryId == subcategory.shopCategoryId);

          if (subcategory.shopParentCategoryID == currentShopCategoryID) {
            if (findSubCtg === undefined) {
              this.categories.splice(indexCtg, 0, subcategory);
              currentCategory.show = true;
              currentCategory.deploy = false;
            } else {
              this.categories.forEach(target => {
                if (target.shopCategoryId == subcategory.shopCategoryId) {
                  target.show = !target.show;
                }
              })
            }
          }
        } else {
          currentCategory.deploy = null;
        }
      }
    }
  },
  created() {
    if (this.categories.length === 0) {
      // call php
    }
  },
  watch: {
  },
});
</script>

<style lang="scss" scoped>
.display-table-matchingFb {

  .subcategorie-1 {
    td:first-child {
      padding-left: 30px;
    }
  }
}
</style>
