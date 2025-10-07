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
{include file='./head.tpl'}

<link href="https://unpkg.com/prestashop_accounts_vue_components@5" rel=preload as=script>
<link href="https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js" rel=preload as=script>

<div id="psFacebookApp"></div>

<script src="{$psAccountsCdnUrl|escape:'htmlall':'UTF-8'}"></script>
<script src="https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js"></script>
<script src="https://unpkg.com/@prestashopcorp/billing-cdc/dist/bundle.js" rel=preload></script>

{if $psSocialLiveMode}
  <script type="module" src="http://localhost:5173/@vite/client"></script>
  <script type="module" src="http://localhost:5173/src/main.ts"></script>
{else}
  <link href="{$pathApp|escape:'htmlall':'UTF-8'}" as=script>
  <script src="{$pathApp|escape:'htmlall':'UTF-8'}" type="module"></script>
{/if}

<script>
// it's used to avoid breaking the menu on the module
document.addEventListener('DOMContentLoaded', function () {
  const hasNoBootstrapClass = document.querySelector('#content.nobootstrap');
  if (hasNoBootstrapClass === null) {
    document.querySelector('#content').classList.add('nobootstrap');
    document.querySelector('#content').classList.remove('bootstrap');
  }
});
</script>
