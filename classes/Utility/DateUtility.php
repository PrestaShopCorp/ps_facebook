<?php

namespace PrestaShop\Module\PrestashopFacebook\Utility;

use DateTime;

class DateUtility
{
    /**
     * @param DateTime $currentDate
     * @param DateTime $comparedDate
     * @param int $days
     *
     * @return bool
     */
    public static function isDateNewerThenGivenDays(DateTime $currentDate, DateTime $comparedDate, $days = 7)
    {
        return $currentDate <= $comparedDate->add(new \DateInterval("P{$days}D"));
    }
}
