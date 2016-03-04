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
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class PriceCalculatorExtension extends \Twig_Extension
{

    use ContainerAwareTrait;

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

        $currency = $this->container->get('bordeux.language.manager')->getCurrentLanguage()->getCurrency();

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