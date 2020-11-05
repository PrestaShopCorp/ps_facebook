<?php

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fb_category_match` (
				`id_category` INT(64)  NOT NULL PRIMARY KEY,
				`google_category_id` INT(64) NOT NULL,
				`is_parent_category` TINYINT(1),
				 INDEX (id_category, google_category_id)
			)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
