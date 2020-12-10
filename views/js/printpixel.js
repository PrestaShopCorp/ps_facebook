/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
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
            error: function(err) {}
        });
    }

    // support 1.6 version
    if (typeof(prestashop) == 'undefined') {
        return;
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

    // Track CustomizeProduct event for PS1.7
    prestashop.on('updatedProduct', function(params) {
        if (typeof(params) !== 'undefined') {
            fbq('track', 'CustomizeProduct');
        }
    })

    //Track product added to a wishlist
    prestashop.on('wishlistEventBusInit', () => {
        window.WishlistEventBus.$on('addedToWishlist', (params) => {
            fbq('track', 'AddToWishlist', {id_produit: params.detail.idProduct, content_type: "product"});
        })
    })

});
