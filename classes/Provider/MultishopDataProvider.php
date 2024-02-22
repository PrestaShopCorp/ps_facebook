<?php
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

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\Repository\ShopRepository;
use PrestaShop\Module\Ps_facebook\Tracker\Segment;
use Shop;

class MultishopDataProvider
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var Segment
     */
    private $segment;

    public function __construct(
        ShopRepository $shopRepository,
        Segment $segment
    ) {
        $this->shopRepository = $shopRepository;
        $this->segment = $segment;
    }

    /**
     * It appeared that PS Account is currently incompatible with multistore feature.
     * While a new major version is prepared, we display a message if the merchant
     * onboarded one other shop.
     *
     * To revent this, we check if a shop is already onboarded and
     * warn the merchant accordingly.
     *
     * @param Shop $currentShop
     *
     * @return bool
     */
    public function isCurrentShopInConflict(Shop $currentShop)
    {
        $configurationData = $this->shopRepository->getShopDomainsAndConfiguration();

        foreach ($configurationData as $shopData) {
            if ((int) $shopData['id_shop'] === (int) $currentShop->id) {
                continue;
            }

            if (empty($shopData['acces_token_value'])) {
                continue;
            }

            $this->segment->setMessage('[FBK] Error: Warn about multistore incompatibility with PS Account');
            $this->segment->track();

            return true;
        }

        return false;
    }
}
