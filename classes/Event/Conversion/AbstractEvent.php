<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Context;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use PrestaShop\Module\PrestashopFacebook\Event\ConversionEventInterface;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;

abstract class AbstractEvent implements ConversionEventInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var false|string
     */
    protected $pixelId;

    public function __construct(Context $context, $pixelId)
    {
        $this->context = $context;
        $this->pixelId = $pixelId;
    }

    /**
     * @return UserData
     */
    protected function createSdkUserData()
    {
        $customerInformation = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';

        $userData = (new UserData())
            ->setFbp($fbp)
            ->setFbc($fbc)
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setEmail($customerInformation['email'])
            ->setFirstName($customerInformation['firstname'])
            ->setLastName($customerInformation['lastname'])
            ->setPhone($customerInformation['phone'])
            ->setDateOfBirth($customerInformation['birthday'])
            ->setCity($customerInformation['city'])
            ->setState($customerInformation['stateIso'])
            ->setZipCode($customerInformation['postCode'])
            ->setCountryCode($customerInformation['countryIso'])
            ->setGender($customerInformation['gender']);

        return $userData;
    }

    protected function sendEvents(array $events)
    {
        $request = (new EventRequest($this->pixelId))
            ->setEvents($events);

        try {
            $request->execute();
        } catch (\Exception $e) {
            //todo: need to logg exception
        }
    }
}
