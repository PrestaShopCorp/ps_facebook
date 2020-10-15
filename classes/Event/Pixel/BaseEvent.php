<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Pixel;

use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;

abstract class BaseEvent
{
    /**
     * @var \Context
     */
    protected $context;

    /**
     * @var \Ps_facebook
     */
    protected $module;

    public function __construct(\Context $context, \Ps_facebook $module)
    {
        $this->context = $context;
        $this->module = $module;
    }

    /**
     * formatPixel
     *
     * @param array $params
     *
     * @return string|false
     */
    protected function formatPixel($params)
    {
        return json_encode($params);
    }

    /**
     * getCustomerInformations
     *
     * @return array
     */
    protected function getCustomerInformation()
    {
        $customerInformation = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $arrayReturned = [];
        $arrayReturned['ct'] = $customerInformation['city'];
        $arrayReturned['country'] = $customerInformation['country_iso'];
        $arrayReturned['zp'] = $customerInformation['postcode'];
        $arrayReturned['ph'] = $customerInformation['phone'];
        $arrayReturned['gender'] = $customerInformation['gender'];
        $arrayReturned['fn'] = $customerInformation['first_name'];
        $arrayReturned['ln'] = $customerInformation['last_name'];
        $arrayReturned['em'] = $customerInformation['email'];

        // data structured for pixel
        return $arrayReturned;
    }
}
