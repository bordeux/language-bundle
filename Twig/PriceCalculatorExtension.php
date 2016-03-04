<?php
/**
 * @author Krzysztof Bednarczyk
 * User: devno
 * Date: 21.02.2016
 * Time: 14:15
 */

namespace Bordeux\LanguageBundle\Twig;


use Bordeux\LanguageBundle\Entity\Money;
use Bordeux\LanguageBundle\Service\LanguageManager;

class PriceCalculatorExtension extends \Twig_Extension
{

    /**
     * @var LanguageManager
     */
    protected $languageManager;


    /**
     * Get languageManager value
     * @author Krzysztof Bednarczyk
     * @return LanguageManager
     */
    public function getLanguageManager()
    {
        return $this->languageManager;
    }

    /**
     * Set languageManager value
     * @author Krzysztof Bednarczyk
     * @param LanguageManager $languageManager
     * @return  $this
     */
    public function setLanguageManager($languageManager)
    {
        $this->languageManager = $languageManager;
        return $this;
    }

    /**
     * @author Krzysztof Bednarczyk
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('priceCalculator', array($this, 'priceCalculator')),
        );
    }

    /**
     * @author Krzysztof Bednarczyk
     * @param Money|null $money
     * @return null
     */
    public function priceCalculator($money)
    {
        if (!$money || !($money instanceof Money)) {
            return null;
        }

        $currency = $this->languageManager->getCurrentLanguage()->getCurrency();

        if ($money->getCurrency()->getId() === $currency->getId()) {
            return $money->format();
        }

        return $money->format() . ' / ' . $currency->convert($money)->format();
    }

    public function getName()
    {
        return 'bordeux_language_price_extension';
    }
}