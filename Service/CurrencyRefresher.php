<?php

namespace Bordeux\LanguageBundle\Service;

use Curl\Curl;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DomCrawler\Crawler;


/**
 * Class CurrencyRefresher
 * @author Krzysztof Bednarczyk
 * @package Bordeux\LanguageBundle\Service
 */
class CurrencyRefresher
{
    use ContainerAwareTrait;


    public function refresh()
    {
        $em = $this->container->get("doctrine")->getManager();


        $currencyList = $em->getRepository("BordeuxLanguageBundle:Currency")->findAll();


        $date = (new \DateTime())->format("Ymd");
        $curl = new Curl();
        $json = $curl->get("http://finance.yahoo.com/connection/currency-converter-cache?date={$date}");
        preg_match_all('/\((.*)\);/si', $json, $json, PREG_PATTERN_ORDER);
        $json = $json[1][0];
        $json = json_decode($json, true);


        foreach ($json['list']['resources'] as $item) {
            $item = ($item['resource']['fields']);

            foreach ($currencyList as $currency) {
                if ($item['name'] === "USD/" . $currency->getSymbol()) {
                    $currency->setValue((float)$item['price']);
                    $currency->setLastUpdate(new \DateTime($item['utctime']));
                }
            }
        }

        $em->flush();

        return true;
    }

}

