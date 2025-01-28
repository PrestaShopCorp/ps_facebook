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

namespace PrestaShop\Module\PrestashopFacebook\API\Client;

use PrestaShop\Module\PrestashopFacebook\API\EventSubscriber\ApiErrorSubscriber;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookClientException;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;
use PrestaShop\Module\PrestashopFacebook\Http\HttpClient;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class FacebookCategoryClient
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    public function __construct(
        ApiClientFactoryInterface $apiClientFactory,
        GoogleCategoryRepository $googleCategoryRepository
    ) {
        $this->client = $apiClientFactory->createClient();
        $this->googleCategoryRepository = $googleCategoryRepository;
    }

    /**
     * @param int $categoryId
     * @param int $shopId
     *
     * @return array|null
     */
    public function getGoogleCategory($categoryId, $shopId)
    {
        $googleCategoryId = $this->googleCategoryRepository->getGoogleCategoryIdByCategoryId($categoryId, $shopId);

        if (empty($googleCategoryId)) {
            return null;
        }

        $googleCategory = $this->get('taxonomy/' . $googleCategoryId);

        if (!is_array($googleCategory)) {
            return null;
        }

        return reset($googleCategory);
    }

    protected function get($id, array $fields = [], array $query = [])
    {
        $query = array_merge(
            [
                'fields' => implode(',', $fields),
            ],
            $query
        );

        $response = $this->client->get("/{$id}" . http_build_query($query));

        if (!$response->isSuccessful()) {
            (new ApiErrorSubscriber())->onParsedResponse($response, ['exceptionClass' => FacebookClientException::class]);
        }

        return $response->getBody();
    }
}
