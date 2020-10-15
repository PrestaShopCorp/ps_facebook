<?php

namespace utility;

use Customer;
use PrestaShop\Module\Ps_facebook\Utility\CustomerInformationUtility;

class CustomerInformationUtilityTest extends \PHPUnit_Framework_TestCase
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
        $customer = $this->getMockBuilder(Customer::class)
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
            'case1' => [
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
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'result' => [
                    'city' => 'kaunas',
                    'country_iso' => 'lv',
                    'postcode' => '3003',
                    'phone' => '12345678',
                    'gender' => 'm',
                    'birthday' => strtolower($birthday),
                    'first_name' => strtolower($firstName),
                    'last_name' => strtolower($lastName),
                    'email' => strtolower($email),
                ],
            ],
        ];
    }
}
