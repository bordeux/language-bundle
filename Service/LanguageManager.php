<?php

namespace Bordeux\LanguageBundle\Service;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Bordeux\LanguageBundle\Entity\Language;
use Bordeux\LanguageBundle\Entity\LanguageToken;
use Bordeux\LanguageBundle\Entity\LanguageTranslation;
use Bordeux\LanguageBundle\Translation\Translator;

/**
 * Class LanguageManager
 * @author Krzysztof Bednarczyk
 * @package Bordeux\LanguageBundle\Service
 */
class LanguageManager
{
    use ContainerAwareTrait;


    /**
     * @var Language[]
     */
    protected $languages;


    /**
     * @var CacheProvider
     */
    protected $cache;


    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;


    /**
     * @var string
     */
    protected $defaultLanguage = "en";


    /**
     * @var Translator
     */
    protected $translator;

    /**
     * LanguageManager constructor.
     * @author Krzysztof Bednarczyk
     */
    public function __construct(ContainerInterface $containerInterface)
    {
        $this->setContainer($containerInterface);
        $this->doctrine = $this->container->get("doctrine");
        $this->cache = $this->container->get("cache");
        $this->languages = $this->getLanguagesList();
        $this->translator = $this->container->get("translator");
    }


    /**
     * @author Krzysztof Bednarczyk
     * @return Language[]
     */
    private function getLanguagesList($reload = false)
    {
        $key = "language_list_cache";

        if ($this->cache->contains($key) && !$reload) {
            return $this->cache->fetch($key);
        }

        /** @var Language[] $list */
        $list = $this->doctrine->getRepository("BordeuxLanguageBundle:Language")
            ->createQueryBuilder("l")
            ->join("l.currency", "c")->addSelect("c")
            ->getQuery()
            ->getResult();

        $newList = [];
        foreach ($list as $language) {
            $newList[$language->getLocale()] = $language;
        }

        $this->cache->save($key, $newList, 3600);

        return $newList;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param string $locale
     * @return bool
     */
    public function hasLocale($locale)
    {
        return isset($this->languages[$locale]);
    }


    /**
     * @author Krzysztof Bednarczyk
     * @return $this
     */
    public function clearLanguageList()
    {
        $this->getLanguagesList(true);
        return $this;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param Language|null $language
     * @return $this|LanguageManager
     */
    public function clearTranslationCache(Language $language = null)
    {
        $cacheDir = $this->translator->getOptions()['cache_dir'];

        $finder = (new \Symfony\Component\Finder\Finder())->in($cacheDir);
        if ($language) {
            $finder = $finder->contains(".{$language->getLocale()}.");
        }

        /** @var \SplFileInfo $file */
        foreach ($finder->files() as $file) {
            @unlink($file->getRealPath());
        }

        if ($language && $language->getAlias()) {
            return $this->clearTranslationCache(
                $language->getAlias()
            );
        }


        return $this;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param string $locale
     * @return Language
     */
    public function getLanguage($locale)
    {
        return isset($this->languages[$locale]) ? $this->languages[$locale] : $this->languages[$this->defaultLanguage];
    }

    /**
     * @author Krzysztof Bednarczyk
     * @return Language
     */
    public function getCurrentLanguage()
    {
        return $this->getLanguage(
            $this->container->get("translator")->getLocale()
        );
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param Language $language
     * @param string $token
     * @param string $domain
     * @return bool
     */
    public function createTranslation(Language $language, $token, $domain)
    {

        $translation = new LanguageTranslation();
        $translation->setLanguage($language->getAlias() ?: $language);
        $translation->setTranslation($token);
        $translation->setLanguageToken($this->getToken(
            $token
        ));


        $em = $this->doctrine->getManager();
        try {
            $em->persist($translation);
            $em->flush([
                $translation
            ]);
        } catch (\Exception $e) {
            $em->detach($translation);
        }


        return true;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param string $id
     * @return null|object|LanguageToken
     */
    public function getToken($id)
    {
        $token = $this->doctrine
            ->getRepository("BordeuxLanguageBundle:LanguageToken")
            ->findOneBy([
                "token" => $id
            ]);
        if ($token) {
            return $token;
        }

        $token = new LanguageToken();
        $token->setToken($id);
        $em = $this->doctrine->getManager();
        $em->persist($token);
        $em->flush([
            $token
        ]);
        return $token;
    }


}

