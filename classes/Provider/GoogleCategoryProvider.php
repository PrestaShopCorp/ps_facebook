<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use PrestaShop\Module\PrestashopFacebook\API\FacebookCategoryClient;

class GoogleCategoryProvider implements GoogleCategoryProviderInterface
{
    /**
     * @var FacebookCategoryClient
     */
    private $facebookCategoryClient;

    public function __construct(FacebookCategoryClient $facebookCategoryClient)
    {
        $this->facebookCategoryClient = $facebookCategoryClient;
    }

    public function getGoogleCategory($categoryId)
    {
        return $this->facebookCategoryClient->getGoogleCategory($categoryId);
    }
}
