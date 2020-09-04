<?php

namespace PrestaShop\Module\PrestashopFacebook\Utility;

use DateTime;

class DateUtility
{
    const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';
    const NOT_SELECTED_DATE = '0000-00-00 00:00:00';
    const NOT_SELECTED_DATE_FORMATTED = '0000-00-00T00:00:00';

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

    /**
     * @param string $date
     * @param string $format
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function formattedDate($date, $format = self::DATE_TIME_FORMAT)
    {
        if ($date === self::NOT_SELECTED_DATE) {
            return self::NOT_SELECTED_DATE_FORMATTED;
        }

        return (new DateTime($date))->format($format);
    }
}
