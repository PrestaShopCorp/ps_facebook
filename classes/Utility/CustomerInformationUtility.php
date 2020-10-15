<?php

namespace PrestaShop\Module\Ps_facebook\Utility;

use Customer;
use FacebookAds\Object\ServerSide\Gender;

class CustomerInformationUtility
{
    public static function getCustomerInformationForPixel(Customer $customer)
    {
        $simpleAddresses = $customer->getSimpleAddresses();

        if (count($simpleAddresses) > 0) {
            $address = reset($simpleAddresses);
            if ($address['city'] != null) {
                $arrayReturned['city'] = strtolower($address['city']);
            }
            if ($address['country_iso'] != null) {
                $arrayReturned['country_iso'] = strtolower($address['country_iso']);
            }
            if ($address['postcode'] != null) {
                $arrayReturned['postcode'] = preg_replace('/[^0-9.]+/', '', $address['postcode']);
            }
            if ($address['phone'] != null) {
                $arrayReturned['phone'] = preg_replace('/[^0-9.]+/', '', $address['phone']);
            }
        }

        $gender = null;
        if ($customer->id_gender !== null) {
            //todo: better to check gender type
            $gender = (int) $customer->id_gender === 1 ? Gender::MALE : Gender::FEMALE;
        }

        $arrayReturned['gender'] = $gender;

        $birthDate = \DateTime::createFromFormat('Y-m-d', $customer->birthday);
        if ($birthDate instanceof \DateTime) {
            $arrayReturned['birthday'] = $birthDate->format('Ymd');
        }

        $arrayReturned['first_name'] = strtolower($customer->firstname);
        $arrayReturned['last_name'] = strtolower($customer->lastname);
        $arrayReturned['email'] = strtolower($customer->email);

        return $arrayReturned;
    }
}
