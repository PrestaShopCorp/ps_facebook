<?php

function upgrade_module_1_2_0()
{
    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` DROP PRIMARY KEY;';
    $sql .= 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD `id_shop` INT(11) NOT NULL AFTER `is_parent_category`;';
    $sql .= 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD PRIMARY KEY (id_category, id_shop);';

    return Db::getInstance()->execute($sql);
}
