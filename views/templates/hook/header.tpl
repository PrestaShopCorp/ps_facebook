{*
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
</script>

<noscript>
    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={/literal}{$id_pixel|escape:'htmlall':'UTF-8'}{literal}&ev=PageView&noscript=1"/>
</noscript>
{/literal}
<!-- End Facebook Pixel Code -->

<!-- Set Facebook Pixel Product Export -->
{if $page.page_name == 'product'}
    <meta property="og:title" content="{$product.name|escape:'htmlall':'UTF-8'}">
    <meta property="og:description" content="{$product.description_short|strip_tags:false|escape:'htmlall':'UTF-8'}">
    <meta property="og:url" content="{$product.link nofilter}">
    <meta property="og:image" content="{$product.images.0.bySize.medium_default.url}">
    <meta property="product:brand" content="{$product_manufacturer->name}">
    <meta property="product:availability" content="{if $product.available_for_order == 1}In stock{else}Out of stock{/if}">
    <meta property="product:condition" content="{$product.embedded_attributes.condition}">
    <meta property="product:price:amount" content="{$product.price_amount}">
    <meta property="product:price:currency" content="{$currency.iso_code}">
    <meta property="product:retailer_item_id" content="{$product.id}">
{/if}
<!-- END OF Set Facebook Pixel Product Export -->

{if !empty($content)}
    {literal}
        <script>
            fbq('{/literal}{$track|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$type|escape:'htmlall':'UTF-8'}{literal}', {/literal}{$content nofilter}{literal});
        </script>
    {/literal}
{else if !empty($type) && empty($content)}
    {literal}
        <script>
            fbq('{/literal}{$track|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$type|escape:'htmlall':'UTF-8'}{literal}');
        </script>
    {/literal}
{/if}
