<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Address;
use Context;
use Country;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\Gender;
use FacebookAds\Object\ServerSide\UserData;
use Gender as PsGender;
use PrestaShop\Module\PrestashopFacebook\Event\ConversionEventInterface;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;
use State;

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
     * @param Context $context
     *
     * @return UserData
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function createSdkUserData()
    {
        $customerInformation = CustomerInformationUtility::getCustomerInformationForPixel($this->context->customer);

        $customer = $this->context->customer;
        $addressId = Address::getFirstCustomerAddressId($customer->id);
        $address = new Address($addressId);
//        $simpleAddresses = $this->context->customer->getSimpleAddresses();
//        if (count($simpleAddresses) > 0) {
//        }
        $gender = null;
        if ($this->context->customer->id_gender) {
            $psGender = new PsGender($this->context->customer->id_gender, $this->context->language->id);
            $gender = (int) $psGender->type === 1 ? Gender::FEMALE : Gender::MALE;
        }
        $country = new Country($address->id_country);

        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : '';
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : '';

        $userData = (new UserData())
            ->setFbc($fbc)
            // It is recommended to send Client IP and User Agent for ServerSide API Events.
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setFbp($fbp)
            ->setEmail(strtolower($customer->email))
            ->setFirstName(strtolower($customer->firstname))
            ->setLastName(strtolower($customer->lastname))
            ->setPhone(preg_replace('/[^0-9.]+/', '', $address->phone))
            ->setDateOfBirth(preg_replace('/[^0-9.]+/', '', $customer->birthday))
            ->setCity(strtolower($address->city))
            ->setState(strtolower((new State($address->id_state))->iso_code))
            ->setZipCode(preg_replace('/[^0-9.]+/', '', $address->postcode))
            ->setCountryCode(strtolower($country->iso_code));

        if ($gender !== null) {
            $userData->setGender($gender);
        }

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
