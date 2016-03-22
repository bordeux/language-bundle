<?php
/**
 * @author Krzysztof Bednarczyk
 * User: devno
 * Date: 3/22/2016
 * Time: 1:23 AM
 */

namespace Bordeux\LanguageBundle\Service;


use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class LanguageFilesWarmer implements CacheWarmerInterface
{

    /**
     * @var LanguageManager
     */
    protected $languageManager;

    /**
     * LanguageFilesWarmer constructor.
     * @author Krzysztof Bednarczyk
     * @param LanguageManager $languageManager
     */
    public function __construct(LanguageManager $languageManager)
    {
        $this->languageManager = $languageManager;
    }


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
        $this->languageManager->generateLanguageFiles(true);
    }
}