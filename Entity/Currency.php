<?php

namespace Bordeux\LanguageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Currency
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="language__currency")
 * @ORM\Entity(repositoryClass="Bordeux\LanguageBundle\Repository\CurrencyRepository")
 */
class Currency
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="symbol", type="string", length=4, unique=true)
     */
    private $symbol;

    /**
     * @var string
     *
     * @ORM\Column(name="short_symbol", type="string", length=4)
     */
    private $shortSymbol;


    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=50)
     */
    private $format;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;


    /**
     * @var integer
     *
     * @ORM\Column(name="decimals", type="integer")
     */
    private $decimals;

    /**
     * @var string
     *
     * @ORM\Column(name="decimals_separator", type="string", length=20)
     */
    private $decimalsSeparator;

    /**
     * @var string
     *
     * @ORM\Column(name="thousands_separator", type="string", length=20)
     */
    private $thousandsSeparator;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_date", type="datetime", nullable=true)
     */
    private $lastUpdate;


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
     * Set name
     *
     * @param string $name
     * @return Currency
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
     * Set symbol
     *
     * @param string $symbol
     * @return Currency
     */
    public function setSymbol($symbol)
    {
        $this->symbol = strtoupper($symbol);

        return $this;
    }

    /**
     * Get symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return Currency
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get shortSymbol value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getShortSymbol()
    {
        return $this->shortSymbol;
    }

    /**
     * Set shortSymbol value
     * @author Krzysztof Bednarczyk
     * @param string $shortSymbol
     * @return  $this
     */
    public function setShortSymbol($shortSymbol)
    {
        $this->shortSymbol = $shortSymbol;
        return $this;
    }

    /**
     * Get lastUpdate value
     * @author Krzysztof Bednarczyk
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set lastUpdate value
     * @author Krzysztof Bednarczyk
     * @param \DateTime $lastUpdate
     * @return  $this
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    /**
     * Get decimals value
     * @author Krzysztof Bednarczyk
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * Set decimals value
     * @author Krzysztof Bednarczyk
     * @param int $decimals
     * @return  $this
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * Get thousandsSeparator value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * Set thousandsSeparator value
     * @author Krzysztof Bednarczyk
     * @param string $thousandsSeparator
     * @return  $this
     */
    public function setThousandsSeparator($thousandsSeparator)
    {
        $this->thousandsSeparator = $thousandsSeparator;
        return $this;
    }

    /**
     * Get decimalsSeparator value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getDecimalsSeparator()
    {
        return $this->decimalsSeparator;
    }

    /**
     * Set decimalsSeparator value
     * @author Krzysztof Bednarczyk
     * @param string $decimalsSeparator
     * @return  $this
     */
    public function setDecimalsSeparator($decimalsSeparator)
    {
        $this->decimalsSeparator = $decimalsSeparator;
        return $this;
    }

    /**
     * Get format value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set format value
     * @author Krzysztof Bednarczyk
     * @param string $format
     * @return  $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param Money $money
     * @return string
     */
    public function format(Money $money)
    {
        $value = number_format(
            $money->getAmount(),
            $this->decimals,
            $this->decimalsSeparator,
            $this->thousandsSeparator
        );

        return str_replace("%value%", $value, $this->format);
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param Money $money
     * @return Money
     */
    public function convert(Money $money)
    {
        $converted = new Money();
        $converted->setCurrency($this);

        $converted->setAmount(
            $money->getAmount() *
            ( $this->getValue() / $money->getCurrency()->getValue())
        );

        return $converted;
    }

    function __toString()
    {
        return $this->getName();
    }


}
