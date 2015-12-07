<?php

namespace BasketPlanner\MatchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class ExpiredMatchCommand
 * @package BasketPlanner\MatchBundle\Command
 */
class ExpiredMatchCommand extends ContainerAwareCommand
{

    /**
     * Configuration of the command
     */
    protected function configure()
    {
        $this
            ->setName('match:check-expired')
            ->setDescription('Check for expired matches and then change their status');
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

        $query = $em->createQueryBuilder()
                    ->select("m")
                    ->from("BasketPlannerMatchBundle:Match", "m")
                    ->where("m.active = true")
                    ->andWhere("m.createdAt<CURRENT_TIMESTAMP()");
        
        $outputInterface->writeln('Done');
    }

}