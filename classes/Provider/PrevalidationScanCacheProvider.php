<?php

namespace PrestaShop\Module\PrestashopFacebook\Provider;

use Ps_facebook;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PrevalidationScanCacheProvider
{
    const CACHE_KEY_SUMMARY = 'summary_';
    const CACHE_KEY_PAGE = 'page_';

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
