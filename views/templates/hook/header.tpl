{**
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
 *}

<!-- Facebook Pixel Code -->
{literal}
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.agent='plprestashop-download'; // n.agent to keep because of partnership
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js');

    // Allow third-party modules to disable Pixel
    fbq('consent', !!window.doNotConsentToPixel ? 'revoke' : 'grant');
{/literal}
{strip}
    {if isset($userInfos)}
        {literal}
            fbq('init', '{/literal}{$id_pixel|escape:'htmlall':'UTF-8'}{literal}', {/literal}{$userInfos|@json_encode nofilter}{literal});
        {/literal}
    {else}
        {literal}
            fbq('init', '{/literal}{$id_pixel|escape:'htmlall':'UTF-8'}{literal}');
        {/literal}
    {/if}
{/strip}
{literal}
    fbq('track', 'PageView');
    var pixel_fc = {/literal}"{$pixel_fc|escape:'htmlall':'UTF-8'}"{literal};
</script>

<noscript>
    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={/literal}{$id_pixel|escape:'htmlall':'UTF-8'}{literal}&ev=PageView&noscript=1"/>
</noscript>
{/literal}
<!-- End Facebook Pixel Code -->

<!-- Set Facebook Pixel Product Export -->
{if isset($page) }
  {if $page.page_name == 'product'}
      <meta property="og:type" content="product">
      <meta property="og:url" content="{$urls.current_url}">
      <meta property="og:title" content="{$page.meta.title}">
      <meta property="og:site_name" content="{$shop.name}">
      <meta property="og:description" content="{$page.meta.description}">
      <meta property="og:image" content="{$product.cover.large.url}">
      {if $product.show_price}
          <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
          <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
          <meta property="product:price:amount" content="{$product.price_amount}">
          <meta property="product:price:currency" content="{$currency.iso_code}">
      {/if}
      {if isset($product.weight) && ($product.weight != 0)}
          <meta property="product:weight:value" content="{$product.weight}">
          <meta property="product:weight:units" content="{$product.weight_unit}">
      {/if}
      {if isset($product_manufacturer->id)}
        <meta property="product:brand" content="{$product_manufacturer->name}">
      {/if}
      <meta property="og:availability" content="{$product_availability}">
      <meta property="product:condition" content="{$product.embedded_attributes.condition}">
      <meta property="product:retailer_item_id" content="{$retailer_item_id}">
      <meta property="product:item_group_id" content="{$product.id_product}">
      <meta property="product:category" content="{$item_group_id}"/>
  {/if}
{/if}
<!-- END OF Set Facebook Pixel Product Export -->

{include file="./fbTrack.tpl"}
