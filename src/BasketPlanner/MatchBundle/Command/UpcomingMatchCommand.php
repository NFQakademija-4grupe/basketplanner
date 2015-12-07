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
     * @param InputInterface  $inputInterface  An InputInterface instance
     * @param OutputInterface $outputInterface An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $outputInterface->writeln(
            'Notify users for upcoming matches.'
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
                    ->andWhere("m.notified = false")
                    ->setParameter('date1', $date1)
                    ->setParameter('date2', $date2)
                    ->getQuery();
        $upcomingMatches = $query->execute();

        $counter = 0;

        $notificationService = $this->getContainer()->get('basketplanner_user.notifications_service');
        $router = $this->getContainer()->get('router');

        foreach($upcomingMatches as $match){
            $players = $match->getPlayers();
            $subject = 'BasketPlanner - artėjantis mačas.';
            $url = $router->generate('basket_planner_match_show', ['id' => $match->getId()], true);

            foreach($players as $player) {
                $message = 'Sveiki ' . $player->getFullName() . ', norime informuoti, jog šiandien įvyks mačas, kuriame Jūs dalyvaujate.
                     Norėdami peržiūrėti prisijungusius žaidėjus ar kitą informaciją spauskite ant nuorodos:
                     <a href="'.$url.'">Mačo peržiūra</a>';
                $notificationService->sendNotification($player->getEmail(), $subject, $message);
            }

            $match->setNotified(true);
            $em->persist($match);
            $em->flush();

            $counter++;
        }

        $outputInterface->writeln('Done. Total <info>' . $counter . '</info> upcoming matches notified.');
    }

}