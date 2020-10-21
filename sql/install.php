<?php

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fb_google_category` (
				`id_fb_google_category` INT(64)  NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`google_category_id` INT(64) NOT NULL,
				`parent_id` INT(64),
				`name` VARCHAR(128) NOT NULL,
				`search_string` VARCHAR(128) NOT NULL,
				 INDEX (google_category_id, parent_id)
			)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fb_category_match` (
				`id_category` INT(64)  NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`google_category_id` INT(64) NOT NULL,
				 INDEX (id_category, google_category_id)
			)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
