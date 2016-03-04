<?php

namespace Bordeux\LanguageBundle\Cache;


use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

/**
 * Class LanguageCacheWarmer
 * @author Krzysztof Bednarczyk
 * @package Bordeux\LanguageBundle\Cache
 */
class LanguageCacheWarmer extends CacheWarmer
{
    use ContainerAwareTrait;

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $kernel = $this->container->get('kernel');
        $path = $kernel->locateResource('@BordeuxLanguageBundle/Resources/translations');


        $finder = (new \Symfony\Component\Finder\Finder())->in($path)->contains(".xv");


        /** @var \SplFileInfo $file */
        foreach ($finder->files() as $file) {
            @unlink($file->getRealPath());
        }


        foreach ($this->getLanguagesList() as $language) {
            file_put_contents(
                $path . DIRECTORY_SEPARATOR . "messages.{$language->getLocale()}.xv",
                "hello"
            );
        }

        return true;
    }
}