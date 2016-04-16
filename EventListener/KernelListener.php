<?php

namespace Bordeux\LanguageBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Bordeux\LanguageBundle\Entity\LanguageTranslation;
use Bordeux\LanguageBundle\Translation\Translator;
use Bordeux\UserBundle\Entity\User;


/**
 * Class KernelListener
 * @author Krzysztof Bednarczyk
 * @package Bordeux\LanguageBundle\EventListener
 */
class KernelListener
{
    use ContainerAwareTrait;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var \Bordeux\LanguageBundle\Service\LanguageManager
     */
    protected $manager;

    /**
     * KernelControllerListener constructor.
     * @author Krzysztof Bednarczyk
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->translator = $this->container->get("translator");
        $this->manager = $this->container->get("bordeux.language.manager");
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $token = $this->container->get("security.token_storage")->getToken();


        /** @var User $user */
        if ($token && ($user = $token->getUser()) && $user->getLanguage()) {
            $request->setLocale(
                $user->getLanguage()->getLocale()
            );
            $this->translator->setLocale($request->getLocale());
            return;
        }

        $languages = $event->getRequest()->getLanguages();


        foreach ($languages as $locale) {
            if ($this->manager->hasLocale($locale)) {
                $this->translator->setLocale($locale);
            }
        }

    }

    /**
     * Save new messages
     *
     * @author Krzysztof Bednarczyk
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }


        $messages = $this->translator->getCollectedMessages();
        if (empty($messages)) {
            return;
        }

        $doctrine = $this->container->get("doctrine");
        
        $doctrine->resetManager();


        //->resetEntityManager();

        $languagesToClear = [];
        foreach ($messages as $message) {
            if ($message['state'] !== 1) {
                continue;
            }

            if (!$this->manager->hasLocale($message['locale'])) {
                continue;
            }

            $language = $this->manager->getLanguage($message['locale']);
            $languagesToClear[$language->getId()] = $language;
            $this->manager->createTranslation(
                $language,
                $message['id'],
                $message['domain']
            );
        }

        foreach ($languagesToClear as $language) {
            $this->manager->clearTranslationCache($language);
        }

    }
}
