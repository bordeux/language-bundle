<?php

namespace Bordeux\LanguageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="Bordeux\LanguageBundle\Repository\LanguageRepository")
 */
class Language
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=255, unique=true)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\LanguageBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=false)
     */
    private $currency;


    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=true)
     */
    private $alias;


    /**
     * Language constructor.
     * @author Krzysztof Bednarczyk
     */
    public function __construct()
    {
        $this->active = true;
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
     * Set locale
     *
     * @param string $locale
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Language
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get active value
     * @author Krzysztof Bednarczyk
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set active value
     * @author Krzysztof Bednarczyk
     * @param boolean $active
     * @return  $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get currency value
     * @author Krzysztof Bednarczyk
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set currency value
     * @author Krzysztof Bednarczyk
     * @param Currency $currency
     * @return  $this
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get alias value
     * @author Krzysztof Bednarczyk
     * @return Language
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias value
     * @author Krzysztof Bednarczyk
     * @param Language $alias
     * @return  $this
     */
    public function setAlias($alias)
    {
        if ($alias && $alias->getId() === $this->getId()) {
            throw new \InvalidArgumentException("Recurrency alert!");
        }

        $this->alias = $alias;
        return $this;
    }


    function __toString()
    {
        return $this->getName();
    }


}
