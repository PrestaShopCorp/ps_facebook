<?php

class FBCategoryMatch extends ObjectModel
{
    /**
     * @var int|null
     */
    public $google_category_id;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'fb_category_match',
        'primary' => 'id_category',
        'fields' => [
            'google_category_id' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
        ],
    ];
}
