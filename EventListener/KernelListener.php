<?php

namespace Bordeux\LanguageBundle\EventListener;

use Bordeux\LanguageBundle\Translation\Translator;
use Bordeux\LanguageBundle\User\UserLanguageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\DataCollectorTranslator;


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
     * @var TokenStorage
     */
    protected $tokenStorage;

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
        $this->tokenStorage = $this->container->get("security.token_storage");
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $token = $this->tokenStorage->getToken();


        /**
         * From auth
         */
        /** @var UserLanguageInterface $user */
        if ($token && ($user = $token->getUser()) && ($user instanceof UserLanguageInterface) && $user->getLanguage()) {
            $request->setLocale(
                $user->getLanguage()->getLocale()
            );

            $this->translator->setLocale($request->getLocale());
            return;
        }

        /**
         * Cookies
         */
        $cookieName = $this->container->getParameter("bordeux.language.cookie_name");
        if ($request->cookies->has($cookieName)) {
            $cookieLocale = $request->cookies->get($cookieName) ?: "none";
            if ($this->manager->hasLocale($cookieLocale)) {
                $this->translator->setLocale($cookieLocale);
                return;
            }
        }


        /**
         * From browser header
         */
        $languages = $request->getLanguages();
        foreach ($languages as $locale) {
            if ($this->manager->hasLocale($locale)) {
                $this->translator->setLocale($locale);
                return;
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


        if (!method_exists($this->translator, 'getCollectedMessages')) {
            return;
        }


        $messages = $this->translator->getCollectedMessages();
        if (empty($messages)) {
            return;
        }



        $languagesToClear = [];
        foreach ($messages as $message) {
            if(!in_array((int) $message['state'], [
                DataCollectorTranslator::MESSAGE_MISSING,
                DataCollectorTranslator::MESSAGE_EQUALS_FALLBACK,
            ])){
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
