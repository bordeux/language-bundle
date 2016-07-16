<?php

namespace Bordeux\LanguageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Money
 *
 * @ORM\Table(name="language__money")
 * @ORM\Entity(repositoryClass="Bordeux\LanguageBundle\Repository\MoneyRepository")
 */
class Money
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=4)
     */
    private $amount;

    /**
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\LanguageBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=false)
     */
    private $currency;


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
     * Set amount
     *
     * @param float $amount
     * @return Money
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function format()
    {
        return $this->currency->format($this);
    }

    /**
     * @author Krzysztof Bednarczyk
     * @param Currency $currency
     * @return Money
     */
    public function convert(Currency $currency)
    {
        return $currency->convert($this);
    }

    function __toString()
    {
        return (string)$this->format();
    }


}
