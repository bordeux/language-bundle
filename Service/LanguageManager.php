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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;


    /**
     * @var Language
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
        $this->em = $this->container->get("doctrine")->getManager();
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


        /** @var Language[] $list */
        $list = $this->em->getRepository("BordeuxLanguageBundle:Language")
            ->createQueryBuilder("l")
            ->join("l.currency", "c")->addSelect("c")
            ->orderBy("l.id", "ASC")
            ->getQuery()
            ->setResultCacheId($key)
            ->setResultCacheLifetime(360)
            ->useResultCache(true)
            ->getResult();

        $newList = [];
        $this->defaultLanguage = reset($list);
        foreach ($list as $language) {
            $newList[$language->getLocale()] = $language;
        }

        return $newList;
    }


    /**
     * @return Language
     * @author Krzysztof Bednarczyk
     */
    public function getDefaultLanguage(){
        return $this->defaultLanguage;
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

        if (!file_exists($cacheDir)) {
            return $this;
        }

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
        return isset($this->languages[$locale]) ? $this->languages[$locale] : $this->languages[$this->defaultLanguage->getLocale()];
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

        try {

            if (!$this->em->isOpen()) {
                $this->em = $this->em->create(
                    $this->em->getConnection(),
                    $this->em->getConfiguration()
                );
            }

            $language = $this->em->getRepository(
                "BordeuxLanguageBundle:Language"
            )->find($language->getId());

            $translation = new LanguageTranslation();
            $translation->setLanguage($language->getAlias() ?: $language);
            $translation->setTranslation($token);
            $translation->setLanguageToken($this->getToken(
                $token
            ));
            $translation->setDomain($domain);


            $this->em->persist($translation);
            $this->em->flush([
                $translation
            ]);


        } catch (\Exception $e) {
            return false;
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
        $md5 = md5($id);
        
        $token = $this->em
            ->getRepository("BordeuxLanguageBundle:LanguageToken")
            ->findOneBy([
                "tokenMd5" => $md5
            ]);
        if ($token) {
            return $token;
        }

        $token = new LanguageToken();
        $token->setToken($id);
        $this->em->persist($token);
        $this->em->flush([
            $token
        ]);
        return $token;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @return bool
     */
    public function generateLanguageFiles($reload = true)
    {

        $kernel = $this->container->get('kernel');
        $path = $kernel->locateResource('@BordeuxLanguageBundle/Resources/translations');


        $finder = (new \Symfony\Component\Finder\Finder())->in($path)->contains(".xv");


        /** @var \SplFileInfo $file */
        foreach ($finder->files() as $file) {
            @unlink($file->getRealPath());
        }


        foreach ($this->getLanguagesList($reload) as $language) {
            file_put_contents(
                $path . DIRECTORY_SEPARATOR . "messages.{$language->getLocale()}.xv",
                "hello"
            );
        }

        return $this->clearTranslationCache();
    }


    /**
     * @return \Bordeux\LanguageBundle\Entity\Language[]
     * @author Krzysztof Bednarczyk
     */
    public function getLanguages()
    {
        return $this->languages;
    }
}

