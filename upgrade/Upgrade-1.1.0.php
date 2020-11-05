<?php

function upgrade_module_1_1_0()
{
    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'fb_category_match` ADD `is_parent_category` TINYINT(1) NOT NULL  AFTER `google_category_id`;';

    return Db::getInstance()->execute($sql);
}
