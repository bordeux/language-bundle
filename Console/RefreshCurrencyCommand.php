<?php
namespace Bordeux\LanguageBundle\Console;

use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RequestContext;
use Bordeux\SendmailBundle\Entity\Mail;

class RefreshCurrencyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bordeux:language:currency:refresh')
            ->setDescription('Refresh currency values');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Started refreshing currency values");
        $this->getContainer()->get("bordeux.language.currency.refresher")->refresh();
        $output->writeln("Done");
    }
}