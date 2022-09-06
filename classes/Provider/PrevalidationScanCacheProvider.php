<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Ps_facebook;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PrevalidationScanCacheProvider
{
    public const CACHE_KEY_SUMMARY = 'summary_';
    public const CACHE_KEY_PAGE = 'page_';

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param string $cachePath
     */
    public function __construct(Ps_facebook $module, $cachePath)
    {
        $this->cachePath = $cachePath . '/' . $module->name . '/';
        $this->filesystem = new Filesystem();
    }

    /**
     * @param string $cacheKey
     */
    public function get($cacheKey)
    {
        if (!file_exists($this->getCacheFilePath($cacheKey))) {
            return null;
        }

        return file_get_contents($this->getCacheFilePath($cacheKey));
    }

    /**
     * @param string $cacheKey
     * @param string $content
     */
    public function set($cacheKey, $content)
    {
        $this->filesystem->dumpFile($this->getCacheFilePath($cacheKey), $content);
    }

    public function clear()
    {
        $this->filesystem->mkdir($this->cachePath);

        $finder = Finder::create();
        $files = $finder->files()->in($this->cachePath)->name('*.json');

        foreach ($files as $file) {
            $this->filesystem->remove($file);
        }
    }

    /**
     * @param string $cacheKey
     *
     * @return string
     */
    private function getCacheFilePath($cacheKey)
    {
        return $this->cachePath . $cacheKey . '.json';
    }
}
