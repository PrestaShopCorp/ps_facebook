<?php

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\PrestashopFacebook\Utility\DateUtility;

class DateUtilityTest extends TestCase
{
    /**
     * @dataProvider isDateNewerThenGivenDaysDataProvider
     *
     * @param $currentDate
     * @param $comparedDate
     * @param $days
     * @param $result
     */
    public function testIsDateNewerThenGivenDays($currentDate, $comparedDate, $days, $result)
    {
        $isNewer = DateUtility::isDateNewerThenGivenDays($currentDate, $comparedDate, $days);

        self::assertEquals($result, $isNewer);
    }

    public function isDateNewerThenGivenDaysDataProvider()
    {
        return [
            'newer' => [
                'currentDate' => new DateTime('2020-08-05 08:00:00'),
                'comparedDate' => new DateTime('2020-08-01 08:00:00'),
                'days' => 7,
                'result' => true,
            ],
            'newer2' => [
                'currentDate' => new DateTime('2020-08-08 08:00:00'),
                'comparedDate' => new DateTime('2020-08-01 08:00:00'),
                'days' => 7,
                'result' => true,
            ],
            'older' => [
                'currentDate' => new DateTime('2020-08-09 08:00:00'),
                'comparedDate' => new DateTime('2020-08-01 08:00:00'),
                'days' => 7,
                'result' => false,
            ],
            'older2' => [
                'currentDate' => new DateTime('2020-08-08 09:00:00'),
                'comparedDate' => new DateTime('2020-08-01 08:00:00'),
                'days' => 7,
                'result' => false,
            ],
        ];
    }
}
