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

namespace PrestaShop\Module\PrestashopFacebook\Handler;

use PrestaShop\Module\PrestashopFacebook\Config\Config;
use PrestaShop\Module\PrestashopFacebook\Provider\PrevalidationScanCacheProvider;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;

class PrevalidationScanRefreshHandler
{
    /**
     * @var PrevalidationScanCacheProvider
     */
    protected $prevalidationScanCacheProvider;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var int
     */
    protected $shopId;

    /**
     * @param int $shopId
     */
    public function __construct(
        PrevalidationScanCacheProvider $prevalidationScanCacheProvider,
        ProductRepository $productRepository,
        $shopId
    ) {
        $this->prevalidationScanCacheProvider = $prevalidationScanCacheProvider;
        $this->productRepository = $productRepository;
        $this->shopId = $shopId;
    }

    /**
     * @param int $page
     *
     * @return array
     */
    public function run($page = 0)
    {
        if ($page === 0) {
            $this->prevalidationScanCacheProvider->clear();
        }

        $products = $this->productRepository->getProductsWithErrors($this->shopId, $page);
        $this->prevalidationScanCacheProvider->set(
            PrevalidationScanCacheProvider::CACHE_KEY_PAGE . $this->shopId . '_' . $page,
            json_encode($products)
        );
        $numberOfProductsWithError = count($products) + Config::REPORTS_PER_PAGE * $page;

        if (count($products) === Config::REPORTS_PER_PAGE) {
            // We reached the maximum number of results, this is likely meaning
            // that we have another page to work with.
            return [
                'success' => true,
                'complete' => false,
                'page_done' => $page,
                'progress' => $numberOfProductsWithError,
            ];
        }

        // This was the last page, we store and return the summary
        $summary = $this->generateSummaryData($numberOfProductsWithError);

        $this->prevalidationScanCacheProvider->set(
            PrevalidationScanCacheProvider::CACHE_KEY_SUMMARY . $this->shopId,
            json_encode($summary)
        );

        return [
            'success' => true,
            'complete' => true,
            'prevalidation' => $summary,
        ];
    }

    /**
     * @param int $numberOfProductsWithError
     *
     * @return array
     */
    private function generateSummaryData($numberOfProductsWithError)
    {
        $productsTotal = $this->productRepository->getProductsTotal($this->shopId, ['onlyActive' => true]);

        return [
            'syncable' => $productsTotal - $numberOfProductsWithError,
            'notSyncable' => $numberOfProductsWithError,
            'lastScanDate' => date(DATE_ISO8601),
        ];
    }
}
