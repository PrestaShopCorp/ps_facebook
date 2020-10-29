<?php
/**
 * 2007-2020 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\Ps_facebook\Helper;

use PrestaShopLogger;

class JsonHelper
{
    /**
     * Encode the data to json and check and force the return to empty string if false
     *
     * @param mixed $data
     *
     * @return string
     */
    public function jsonEncode($data)
    {
        $json = json_encode($data);

        if (false !== $json) {
            return $json;
        }

        PrestaShopLogger::addLog('[PS_FACEBOOK] Unable to encode Json', 3);

        return '';
    }

    /**
     * Check if the json is valid and returns an empty data if not
     *
     * @param mixed $json
     * @param bool $assoc
     *
     * @return array $data
     */
    public function jsonDecode($json, $assoc = true)
    {
        $data = json_decode($json, $assoc);

        if (JSON_ERROR_NONE === json_last_error()) {
            return $data;
        }

        PrestaShopLogger::addLog('[PS_FACEBOOK] Unable to decode Json', 3);

        return [];
    }
}
