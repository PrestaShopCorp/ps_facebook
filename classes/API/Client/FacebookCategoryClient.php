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

use GuzzleHttp\Psr7\Request;
use PrestaShop\Module\PrestashopFacebook\API\ResponseListener;
use PrestaShop\Module\PrestashopFacebook\Exception\FacebookClientException;
use PrestaShop\Module\PrestashopFacebook\Factory\ApiClientFactoryInterface;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;
use Prestashop\ModuleLibGuzzleAdapter\Interfaces\HttpClientInterface;

class FacebookCategoryClient
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    /**
     * @var ResponseListener
     */
    private $responseListener;

    public function __construct(
        ApiClientFactoryInterface $apiClientFactory,
        GoogleCategoryRepository $googleCategoryRepository,
        ResponseListener $responseListener
    ) {
        $this->client = $apiClientFactory->createClient();
        $this->googleCategoryRepository = $googleCategoryRepository;
        $this->responseListener = $responseListener;
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

        $request = new Request(
            'GET',
            "/{$id}",
            [
                'query' => $query,
            ]
        );

        $response = $this->responseListener->handleResponse(
            $this->client->sendRequest($request),
            [
                'exceptionClass' => FacebookClientException::class,
            ]
        );

        if (!$response->isSuccessful()) {
            return false;
        }

        return $response->getBody();
    }
}
