/*
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function() {

  function ajaxGetProduct(id, attribute) {
   $.ajax({
       type: 'POST',
       url: pixel_fc,
       dataType: 'json',
       data: {
          action: 'GetProduct',
          ajax: true,
          id_product: id,
          id_attribute: attribute,
       },
       success: function(data) {
          var iso_code = prestashop.currency.iso_code,
          amount = data.price_amount;

          fbq('track', 'AddToCart', {value: amount, currency: iso_code});
       },
       error: function(err) {
       }
   });
  }

  // Track Add to cart
  prestashop.on('updateCart', function(params) {

    if (
      typeof(params) !== 'undefined'
      && typeof(prestashop.cart) !== 'undefined'
    ) {
      var iso_code = prestashop.currency.iso_code,
      products = prestashop.cart.products,
      my_id = params.reason.idProduct,
      my_attribute = params.reason.idProductAttribute,
      my_link = params.reason.linkAction;

      if (my_link != 'delete-from-cart') {

        // Keep ajax call
        // ajaxGetProduct(my_id, my_attribute);

        // Find product
        var search_product = $.grep(products, function(e){
          return e.id_product == my_id && e.id_product_attribute == my_attribute;
        });

        if (products.length != 0) {
          var amount = search_product[0].price_wt;
          fbq('track', 'AddToCart', { value: amount, currency: iso_code, content_ids: my_id, content_type: "product" });
        }

      }
    }
  });

});
