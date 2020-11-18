<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Context;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use PrestaShop\Module\PrestashopFacebook\Repository\ProductRepository;
use PrestaShop\Module\Ps_facebook\Utility\ProductCatalogUtility;

class CustomisationEvent extends AbstractEvent
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        Context $context,
        $pixelId,
        ProductRepository $productRepository
    ) {
        parent::__construct($context, $pixelId);
        $this->productRepository = $productRepository;
    }

    public function send($params)
    {
        $idLang = (int) $this->context->language->id;
        $productId = $params['productId'];
        $attributeIds = $params['attributeIds'];
        $locale = \Tools::strtoupper($this->context->language->iso_code);
        $customData = $this->getCustomAttributeData($productId, $idLang, $attributeIds, $locale);

        $user = $this->createSdkUserData($userData);

        $event = (new Event())
            ->setEventName('CustomizeProduct')
            ->setEventTime(time())
            ->setUserData($user)
            ->setCustomData($customData);

        $events = [];
        $events[] = $event;

        $this->sendEvents($events);
    }

    /**
     * @param int $productId
     * @param int $idLang
     * @param int[] $attributeIds
     * @param string $locale
     *
     * @return CustomData
     *
     * @throws \PrestaShopException
     */
    private function getCustomAttributeData($productId, $idLang, $attributeIds, $locale)
    {
        $attributes = [];
        foreach ($attributeIds as $attributeId) {
            $attributes[] = (new \AttributeCore($attributeId, $idLang))->name;
        }

        $idProductAttribute = $this->productRepository->getIdProductAttributeByIdAttributes(
            $productId,
            $attributeIds
        );

        $psProductId = ProductCatalogUtility::makeProductId($productId, $idProductAttribute, $locale);

        return (new CustomData())
            ->setContentType('product')
            ->setContentIds([$psProductId])
            ->setCustomProperties(
                [
                    'custom_attributes' => $attributes,
                ]
            );
    }
}
