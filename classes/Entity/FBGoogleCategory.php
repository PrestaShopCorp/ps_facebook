<?php

class FBGoogleCategory extends ObjectModel
{
    /**
     * @var int
     */
    public $google_category_id;

    /**
     * @var int|null
     */
    public $parent_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $search_string;

    /**
     * @var array
     */
    public static $definition = [
        'table' => 'fb_google_category',
        'primary' => 'id_fb_google_category',
        'fields' => [
            'google_category_id' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'parent_id' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'search_string' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];
}
