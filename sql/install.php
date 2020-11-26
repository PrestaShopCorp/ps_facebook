<?php

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fb_category_match` (
				`id_category` INT(11) NOT NULL,
				`google_category_id` INT(64) NOT NULL,
				`is_parent_category` TINYINT(1),
				`id_shop` INT(11) NOT NULL,
				 INDEX (id_category, google_category_id),
				 PRIMARY KEY (id_category, id_shop) 
			)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;
