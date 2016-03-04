<?php

namespace Bordeux\LanguageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LanguageTranslation
 *
 * @ORM\Table(name="language__translation",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="translation_unique", columns={"domain", "language_id", "language_token_id"})}
 * )
 * @ORM\Entity(repositoryClass="Bordeux\LanguageBundle\Repository\LanguageTranslationRepository")
 */
class LanguageTranslation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="translation", type="text")
     */
    private $translation;


    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $language;

    /**
     * @var LanguageToken
     *
     * @ORM\ManyToOne(targetEntity="LanguageToken", inversedBy="translations")
     * @ORM\JoinColumn(name="language_token_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $languageToken;


    /**
     * @var boolean
     *
     * @ORM\Column(name="translated", type="boolean")
     */
    private $translated;

    /**
     * LanguageTranslation constructor.
     * @author Krzysztof Bednarczyk
     */
    public function __construct()
    {
        $this->translated = false;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get domain value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain value
     * @author Krzysztof Bednarczyk
     * @param string $domain
     * @return  $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }


    /**
     * Set translation
     *
     * @param string $translation
     * @return LanguageTranslation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;

        if ($this->languageToken && $this->languageToken->getToken() != $translation) {
            $this->translated = true;
        }
        return $this;
    }

    /**
     * Get translation
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Get language value
     * @author Krzysztof Bednarczyk
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language value
     * @author Krzysztof Bednarczyk
     * @param Language $language
     * @return  $this
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get languageToken value
     * @author Krzysztof Bednarczyk
     * @return LanguageToken
     */
    public function getLanguageToken()
    {
        return $this->languageToken;
    }

    /**
     * Set languageToken value
     * @author Krzysztof Bednarczyk
     * @param LanguageToken $languageToken
     * @return  $this
     */
    public function setLanguageToken(LanguageToken $languageToken)
    {
        $this->languageToken = $languageToken;
        return $this;
    }

    /**
     * Get translated value
     * @author Krzysztof Bednarczyk
     * @return boolean
     */
    public function isTranslated()
    {
        return $this->translated;
    }

    /**
     * Set translated value
     * @author Krzysztof Bednarczyk
     * @param boolean $translated
     * @return  $this
     */
    public function setTranslated($translated)
    {
        $this->translated = $translated;
        return $this;
    }

    function __toString()
    {
        return $this->languageToken ? $this->languageToken->__toString() : "";
    }


}
