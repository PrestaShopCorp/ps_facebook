<?php

namespace PrestaShop\Module\PrestashopFacebook\API;

use Exception;
use GuzzleHttp\Client;
use PrestaShop\Module\PrestashopFacebook\Repository\GoogleCategoryRepository;

class FacebookCategoryClient
{
    const API_URL = 'https://facebook-api.psessentials.net';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var GoogleCategoryRepository
     */
    private $googleCategoryRepository;

    public function __construct(Client $client, GoogleCategoryRepository $googleCategoryRepository)
    {
        $this->client = $client;
        $this->googleCategoryRepository = $googleCategoryRepository;
    }

    /**
     * @param $categoryId
     *
     * @return array|null
     */
    public function getGoogleCategory($categoryId)
    {
        $googleCategoryId = $this->googleCategoryRepository->getGoogleCategoryIdByCategoryId($categoryId);

        return $this->call('taxonomy/' . $googleCategoryId);
    }

    protected function call($id, array $fields = [], array $query = [])
    {
        $query = array_merge(
            [
                'fields' => implode(',', $fields),
            ],
            $query
        );

        try {
            $response = $this->client->get(
                self::API_URL . "/{$id}",
                [
                    'query' => $query,
                ]
            );
        } catch (Exception $e) {
            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
