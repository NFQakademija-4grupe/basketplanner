<?php

namespace BasketPlanner\MatchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class UpcomingMatchCommand
 * @package BasketPlanner\MatchBundle\Command
 */
class UpcomingMatchCommand extends ContainerAwareCommand
{

    /**
     * Configuration of the command
     */
    protected function configure()
    {
        $this
            ->setName('match:check-upcoming')
            ->setDescription('Check for upcoming matches and then pass them for notification');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {

    }

    /**
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $outputInterface->writeln(
            'This command does something <info>' . $inputInterface->getOption('env') . '</info> environment'
        );

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');


        $outputInterface->writeln('Done');
    }

}