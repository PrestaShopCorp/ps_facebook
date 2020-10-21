<?php

class FBCategoryMatch extends ObjectModel
{
    /**
     * @var int
     */
    public $id_category;

    /**
     * @var int|null
     */
    public $google_category_id;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'fb_category_match',
        'primary' => 'id_fb_category_match',
        'fields' => [
            'id_category' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'google_category_id' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
        ],
    ];
}
