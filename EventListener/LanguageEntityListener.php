<?php

namespace Bordeux\LanguageBundle\EventListener;

use Bordeux\LanguageBundle\Entity\Language;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class KernelListener
 * @author Krzysztof Bednarczyk
 * @package Bordeux\LanguageBundle\EventListener
 */
class LanguageEntityListener
{
    use ContainerAwareTrait;


    /**
     * @var \Bordeux\LanguageBundle\Service\LanguageManager
     */
    protected $manager;

    /**
     * LanguageEntityListener constructor.
     * @author Krzysztof Bednarczyk
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->manager = $this->container->get("bordeux.language.manager");
    }

    /**
     * @author Krzysztof Bednarczyk
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postShared($args);
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->postShared($args);
    }

    /**
     * @author Krzysztof Bednarczyk
     * @param LifecycleEventArgs $args
     */
    public function postDelete(LifecycleEventArgs $args)
    {
        $this->postShared($args);
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param LifecycleEventArgs $args
     */
    public function postShared(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!($entity instanceof Language)) {
            return;
        }

        $this->container->get("bordeux.language.manager")->generateLanguageFiles();
    }

}
