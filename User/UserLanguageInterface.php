<?php

namespace Bordeux\LanguageBundle\User;

use Bordeux\LanguageBundle\Entity\Language;


/**
 * Interface UserLanguageInterface
 * @author Krzysztof Bednarczyk
 */
interface UserLanguageInterface
{

    /**
     * @return Language
     * @author Krzysztof Bednarczyk
     */
    public function getLanguage();

}