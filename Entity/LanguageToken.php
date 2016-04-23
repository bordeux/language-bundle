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
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;


    /**
     * @var string
     *
     * @ORM\Column(name="token_md5", type="string", length=33, unique=true)
     */
    private $tokenMd5;


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

        $this->tokenMd5 = md5($token);
        
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

    /**
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getTokenMd5()
    {
        return $this->tokenMd5;
    }




    function __toString()
    {
        return $this->getToken();
    }


}
