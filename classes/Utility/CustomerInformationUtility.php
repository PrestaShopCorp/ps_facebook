<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\Ps_facebook\Utility;

use Customer;
use FacebookAds\Object\ServerSide\Gender;
use Tools;

class CustomerInformationUtility
{
    public static function getCustomerInformationForPixel(Customer $customer)
    {
        $simpleAddresses = $customer->getSimpleAddresses();
        $arrayReturned = [];

        if (count($simpleAddresses) > 0) {
            $address = reset($simpleAddresses);
            $arrayReturned['city'] = $address['city'] ? Tools::strtolower($address['city']) : null;
            $arrayReturned['countryIso'] = $address['country_iso'] ? Tools::strtolower($address['country_iso']) : null;
            $arrayReturned['postCode'] = $address['postcode'] ? Tools::strtolower($address['postcode']) : null;

            $arrayReturned['phone'] = $address['phone'] ?
                preg_replace('/[^0-9.]+/', '', $address['phone'])
                : null;

            $arrayReturned['stateIso'] = $address['state_iso'] ? Tools::strtolower($address['state_iso']) : null;
        } else {
            $arrayReturned['city'] = null;
            $arrayReturned['countryIso'] = null;
            $arrayReturned['postCode'] = null;
            $arrayReturned['phone'] = null;
            $arrayReturned['stateIso'] = null;
        }

        $gender = null;
        if ($customer->id_gender !== 0) {
            $genderObj = new \Gender($customer->id_gender);
            if ($genderObj->type !== 2) {
                $gender = (int) $genderObj->type === 0 ? Gender::MALE : Gender::FEMALE;
            }
        }

        $arrayReturned['gender'] = $gender;

        $birthDate = \DateTime::createFromFormat('Y-m-d', $customer->birthday);
        if ($birthDate instanceof \DateTime) {
            $arrayReturned['birthday'] = $birthDate->format('Ymd');
        } else {
            $arrayReturned['birthday'] = null;
        }

        $arrayReturned['firstname'] = $customer->firstname ? Tools::strtolower($customer->firstname) : null;
        $arrayReturned['lastname'] = $customer->firstname ? Tools::strtolower($customer->lastname) : null;
        $arrayReturned['email'] = $customer->firstname ? Tools::strtolower($customer->email) : null;

        return $arrayReturned;
    }
}
