<?php
/**
 * @author Krzysztof Bednarczyk
 * User: devno
 * Date: 27.02.2016
 * Time: 15:43
 */

namespace Bordeux\LanguageBundle\Translation\Loader;


use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Bordeux\LanguageBundle\Entity\Language;
use Bordeux\LanguageBundle\Entity\LanguageTranslation;

class XvLoader implements LoaderInterface
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var Language[]
     */
    protected $cacheLanguages;

    /**
     * XvLoader constructor.
     * @author Krzysztof Bednarczyk
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }


    /**
     * Get doctrine value
     * @author Krzysztof Bednarczyk
     * @return RegistryInterface
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param string $locale
     * @return Language
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLanguage($locale)
    {
        if (isset($this->cacheLanguages[$locale])) {
            return $this->cacheLanguages[$locale];
        }
        $language = $this->getDoctrine()
            ->getRepository("BordeuxLanguageBundle:Language")
            ->createQueryBuilder("l")
            ->leftJoin("l.alias", "a")
            ->andWhere("l.locale = :locale")->setParameter(":locale", $locale)
            ->andWhere("l.active = true")
            ->getQuery()
            ->getOneOrNullResult();

        if ($language) {
            $language = $language->getAlias() ?: $language;
        }

        return $this->cacheLanguages[$locale] = $language;
    }

    /**
     * Loads a locale.
     *
     * @param mixed $resource A resource
     * @param string $locale A locale
     * @param string $domain The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @throws NotFoundResourceException when the resource cannot be found
     * @throws InvalidResourceException  when the resource cannot be loaded
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue($locale);
        $language = $this->getLanguage($locale);

        if (!$language) {
            return $catalogue;
        }

        /** @var LanguageTranslation[] $translations */
        $translations = $this->getDoctrine()
            ->getRepository("BordeuxLanguageBundle:LanguageTranslation")
            ->createQueryBuilder("t")
            ->join("t.languageToken", "token")->addSelect("token")
            ->andWhere("t.language = :language")->setParameter(":language", $language)
            ->getQuery()
            ->getResult();


        $domains = [];
        foreach ($translations as $item) {
            $domains[(string)$item->getDomain()][(string)$item->getLanguageToken()->getToken()] = $item->getTranslation();
        }

        foreach ($domains as $name => $messages) {
            $catalogue->add($messages, $name);
        }

        return $catalogue;
    }
}