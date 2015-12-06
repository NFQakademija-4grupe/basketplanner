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

        $date1 = new \DateTime('+11 hours');
        $date2 = new \DateTime('+12 hours');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQueryBuilder()
                    ->select("m, u")
                    ->from("BasketPlannerMatchBundle:Match", "m")
                    ->leftJoin("m.players", "u")
                    ->where("m.beginsAt > :date1")
                    ->andWhere("m.beginsAt < :date2")
                    ->setParameter('date1', $date1)
                    ->setParameter('date2', $date2);

        $outputInterface->writeln('Done');
    }

}