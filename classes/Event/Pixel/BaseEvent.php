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
        $arrayReturned['country'] = $customerInformation['countryIso'];
        $arrayReturned['zp'] = $customerInformation['postCode'];
        $arrayReturned['ph'] = $customerInformation['phone'];
        $arrayReturned['gender'] = $customerInformation['gender'];
        $arrayReturned['fn'] = $customerInformation['firstname'];
        $arrayReturned['ln'] = $customerInformation['lastname'];
        $arrayReturned['em'] = $customerInformation['email'];
        $arrayReturned['bd'] = $customerInformation['birthday'];
        $arrayReturned['st'] = $customerInformation['stateIso'];

        // data structured for pixel
        return $arrayReturned;
    }
}
