<?php

namespace PrestaShop\Module\Ps_facebook\Environment;

use Dotenv\Dotenv;

class Env
{
    const MODULE_NAME = 'ps_facebook';

    /**
     * Const that define all environment possible to use.
     * Top of the list are taken in first if they exist in the project.
     * eg: If .env.test is present in the module it will be loaded, if not present
     * we try to load the next one etc ...
     *
     * @var array
     */
    const FILE_ENV_LIST = [
        'test' => '.env.test',
        'prod' => '.env',
    ];

    /**
     * Environment name: can be 'prod' or 'test'
     *
     * @var string
     */
    protected $name;

    /**
     * Environment mode: can be 'live' or 'sandbox'
     *
     * @var string
     */
    protected $mode;

    public function __construct()
    {
        foreach (self::FILE_ENV_LIST as $env => $fileName) {
            if (!file_exists(_PS_MODULE_DIR_ . self::MODULE_NAME . '/' . $fileName)) {
                continue;
            }

            $dotenv = Dotenv::create(_PS_MODULE_DIR_ . self::MODULE_NAME . '/', $fileName);
            $dotenv->load();

            $this->setName($env);

            break;
        }
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param string $name
     *
     * @return void
     */
    private function setName($name)
    {
        $this->name = $name;
    }
}
