<?php

namespace PrestaShop\Module\PrestashopFacebook\Event\Conversion;

use Address;
use Configuration;
use Context;
use Country;
use FacebookAds\Object\ServerSide\Gender;
use FacebookAds\Object\ServerSide\UserData;
use PrestaShop\Module\PrestashopFacebook\Event\ConversionEventInterface;
use Gender as PsGender;
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

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->pixelId = Configuration::get('PS_PIXEL_ID');
    }

    /**
     * @param Context $context
     *
     * @return UserData
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function createSdkUserData(Context $context)
    {
        $customer = $context->customer;
        $addressId = Address::getFirstCustomerAddressId($customer->id);
        $address = new Address($addressId);
        $psGender = new PsGender($context->customer->id_gender, $context->language->id);
        $gender = $psGender->type ? Gender::FEMALE : Gender::MALE;
        $country = new Country($address->id_country);

        return (new UserData())
            ->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
            // It is recommended to send Client IP and User Agent for ServerSide API Events.
            ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
            ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setFbp('fb.1.1558571054389.1098115397')
            ->setEmail(strtolower($customer->email))
            ->setFirstName(strtolower($customer->firstname))
            ->setLastName(strtolower($customer->lastname))
            ->setPhone(preg_replace('/[^0-9.]+/', '', $address->phone))
            ->setGender($gender)
            ->setDateOfBirth(preg_replace('/[^0-9.]+/', '', $customer->birthday))
            ->setCity(strtolower($address->city))
            ->setState(strtolower((new State($address->id_state))->iso_code))
            ->setZipCode(preg_replace('/[^0-9.]+/', '', $address->postcode))
            ->setCountryCode(strtolower($country->iso_code));
    }
}
