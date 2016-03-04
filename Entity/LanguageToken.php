<?php

namespace Bordeux\LanguageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LanguageToken
 *
 * @ORM\Table(name="language__token")
 * @ORM\Entity(repositoryClass="Bordeux\LanguageBundle\Repository\LanguageTokenRepository")
 */
class LanguageToken
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
     * @ORM\Column(name="token", type="string", length=255, unique=true)
     */
    private $token;


    /**
     * @var LanguageTranslation[]
     *
     * @ORM\OneToMany(targetEntity="LanguageTranslation", mappedBy="languageToken")
     */
    private $translations;




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
     * Set token
     *
     * @param string $token
     * @return LanguageToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get translations value
     * @author Krzysztof Bednarczyk
     * @return LanguageTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }



    function __toString()
    {
        return $this->getToken();
    }


}
