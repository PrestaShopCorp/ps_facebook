<?php

namespace PrestaShop\Module\PrestashopFacebook\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;

class CustomerInformationUtilityTest extends TestCase
{
    /**
     * @dataProvider getCustomerInformationForPixelDataProvider
     *
     * @param array $simpleAddresses
     * @param array $result
     */
    public function testGetCustomerInformationForPixel(
        $simpleAddresses,
        $genderId,
        $birthday,
        $firstName,
        $lastName,
        $email,
        $result
    ) {
        $customer = $this->getMockBuilder(\Customer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customer->method('getSimpleAddresses')->willReturn($simpleAddresses);
        $customer->id_gender = $genderId;
        $customer->birthday = $birthday;
        $customer->firstname = $firstName;
        $customer->lastname = $lastName;
        $customer->email = $email;

        $response = CustomerInformationUtility::getCustomerInformationForPixel($customer);

        self::assertEquals($result, $response);
    }

    public function getCustomerInformationForPixelDataProvider()
    {
        $birthday = '19960224';
        $birthdayFormatted = '1996-02-24';
        $firstName = 'firstnameTest';
        $lastName = 'lastnameTest';
        $email = 'test@gmail.com';

        return [
            'with simple address' => [
                'simpleAddresses' => [
                    9 => [
                        'id' => '9',
                        'alias' => 'My Address',
                        'firstname' => 'firstnameTest',
                        'lastname' => 'lastnameTest',
                        'company' => 'tes',
                        'address1' => 'zemaiciu 2',
                        'address2' => '',
                        'postcode' => 'LV-3003',
                        'city' => 'kaunas',
                        'id_state' => '0',
                        'state' => null,
                        'state_iso' => null,
                        'id_country' => '125',
                        'country' => 'Latvia',
                        'country_iso' => 'LV',
                        'other' => '',
                        'phone' => '12345678',
                        'phone_mobile' => '',
                        'vat_number' => '',
                        'dni' => '',
                    ],
                ],
                'genderId' => 1,
                'birthday' => $birthdayFormatted,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'result' => [
                    'city' => 'kaunas',
                    'phone' => '12345678',
                    'gender' => 'm',
                    'birthday' => strtolower($birthday),
                    'firstname' => strtolower($firstName),
                    'lastname' => strtolower($lastName),
                    'email' => strtolower($email),
                    'countryIso' => 'lv',
                    'postCode' => 'lv-3003',
                    'stateIso' => null,
                ],
            ],
            'without simple address' => [
                'simpleAddresses' => [],
                'genderId' => 1,
                'birthday' => $birthdayFormatted,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'result' => [
                    'city' => null,
                    'phone' => null,
                    'gender' => 'm',
                    'birthday' => '19960224',
                    'firstname' => 'firstnametest',
                    'lastname' => 'lastnametest',
                    'email' => 'test@gmail.com',
                    'countryIso' => null,
                    'postCode' => null,
                    'stateIso' => null,
                ],
            ],
            'no customer' => [
                'simpleAddresses' => [],
                'genderId' => 0,
                'birthday' => null,
                'firstName' => null,
                'lastName' => null,
                'email' => null,
                'result' => [
                    'city' => null,
                    'phone' => null,
                    'gender' => null,
                    'birthday' => null,
                    'firstname' => null,
                    'lastname' => null,
                    'email' => null,
                    'countryIso' => null,
                    'postCode' => null,
                    'stateIso' => null,
                ],
            ],
        ];
    }
}
