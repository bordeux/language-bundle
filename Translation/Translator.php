<?php
namespace Bordeux\LanguageBundle\Translation;


/**
 * Class Translator
 * @author Krzysztof Bednarczyk
 * @package Bordeux\LanguageBundle\Translation
 */
class Translator extends \Symfony\Bundle\FrameworkBundle\Translation\Translator
{

    public function getOptions()
    {
        return $this->options;
    }
}

