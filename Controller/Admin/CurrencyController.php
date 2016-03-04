<?php

namespace Bordeux\LanguageBundle\Controller\Admin;


use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;


class CurrencyController extends CRUDController
{
    public function refreshAction()
    {
        $this->get("bordeux.language.currency.refresher")->refresh();

        $this->addFlash('sonata_flash_success', 'Currency values refreshed successful');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
