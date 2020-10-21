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

    public function updateGoogleCategories()
    {
        $googleCategoryIds = [];
        $categories = $this->call('taxonomy');
        foreach ($categories as $category) {
            $googleCategoryIds[] = $category['id'];
            $googleCategoryId = $this->googleCategoryRepository->getGoogleCategoryIdByGoogleCategoryId($category['id']);

            $googleCategory = new \FBGoogleCategory($googleCategoryId);
            $googleCategory->google_category_id = $category['id'];
            $googleCategory->parent_id = $category['parentId'];
            $googleCategory->name = $category['name'];
            $googleCategory->search_string = $category['searchString'];

            $googleCategory->save();
        }

        $this->googleCategoryRepository->deleteNotExistingGoogleCategories($googleCategoryIds);
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
