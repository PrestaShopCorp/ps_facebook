<?php

function upgrade_module_1_3_0()
{
    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD `google_category_parent_id` INT(64) NOT NULL AFTER `google_category_id`;';

    return Db::getInstance()->execute($sql);
}
