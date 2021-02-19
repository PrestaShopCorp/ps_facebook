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
function upgrade_module_1_6_0()
{
    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD `google_category_parent_id` INT(64) NOT NULL AFTER `google_category_id`;';
    $sql .= 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD `google_category_parent_name` VARCHAR(255) NOT NULL AFTER `google_category_parent_id`;';
    $sql .= 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD `google_category_name` VARCHAR(255) NOT NULL AFTER `google_category_id`;';

    return Db::getInstance()->execute($sql);
}
