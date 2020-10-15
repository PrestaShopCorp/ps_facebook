<?php

namespace PrestaShop\Module\Ps_facebook\Service;

use Gender;

class GenderService
{
    /**
     * @param $genderId
     *
     * @return int|null
     */
    public static function getGenderType($genderId)
    {
        $gender = null;
        if ($genderId) {
            $gender = new Gender($genderId);

            return (int) $gender->type;
        }

        return null;
    }
}
