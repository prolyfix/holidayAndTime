<?php

namespace App\Command;

use App\Entity\Calendar;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Manager\OvertimeCalculator;
use App\Utility\TimeUtility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calculate-absence-day',
    description: 'Add a short description for your command',
)]
class CalculateAbsenceDayCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $date = new \DateTime();
        $date->modify('-6 month');

        $toilDays = $this->em->getRepository(Calendar::class)->retrieveToilDaysAfter($date);
        dump($toilDays);
        foreach($toilDays as $toilDay) {
            if($toilDay->getUser() !== null)
            {
                $timesheet = $this->createTimesheet($toilDay, $toilDay->getUser());
                if($timesheet !== null)
                {
                    $this->em->persist($timesheet);
                    $this->em->flush();
                }
            }
            elseif($toilDay->getWorkingGroup() !== null)
            {
                foreach($toilDay->getWorkingGroup()->getUsers() as $user)
                {
                    $timesheet = $this->createTimesheet($toilDay, $user);
                    if($timesheet !== null)
                    {
                        $this->em->persist($timesheet);
                        $this->em->flush();
                    }
                }
            } 
            else
            {
                $users = $this->em->getRepository(User::class)->findAll();
                foreach($users as $user)
                {
                    $timesheet = $this->createTimesheet($toilDay, $user);
                    if($timesheet !== null)
                    {
                        $this->em->persist($timesheet);
                        $this->em->flush();
                    }
                }
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
    function createTimesheet($toilDay, $user):?Timesheet
    {
        $toilDayDate = $toilDay->getDate();
        $timesheet = (new Timesheet())->setUser($user)->setStartTime($toilDayDate->setTime(0,0,0))->setEndTime($toilDayDate->setTime(00,00,00));
        if($user->getStartDate() > $timesheet->getStartTime() || ($user->getEndDate() < $timesheet->getEndTime() && $user->getEndDate() !== null)){
            $timesheet->setOvertime(0);
        }
        //todo: verify if Holiday / Bank Holiday / Sickness
        else{
            $hasToWork = OvertimeCalculator::getWorkingHoursForDay($timesheet->getStartTime(), $timesheet->getUser());
            $hasAlreadyWorkedToday = $this->em->getRepository(Timesheet::class)->getAlreadyWorkedToday($timesheet);
            $hasToWorkMinutes = TimeUtility::getMinutesFromTime($hasAlreadyWorkedToday>0?new \DateTime('00:00:00'):$hasToWork);                
            $timesheet->setOvertime(-$hasToWorkMinutes);
            return $timesheet;
        }
    }
}
