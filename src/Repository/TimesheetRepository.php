<?php

namespace App\Repository;

use App\Entity\Commentable;
use App\Entity\Room;
use App\Entity\Timesheet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Timesheet>
 *
 * @method Timesheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timesheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timesheet[]    findAll()
 * @method Timesheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimesheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timesheet::class);
    }

    public function retrieveOvertimeForUser(User $user): float
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.overtime) as total')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        return $qb??0;
    }
    public function retrieveOvertimeForUserForPeriod(User $user, \DateTime $start, \DateTime $end): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.startTime >= :start')
            ->andWhere('t.endTime <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        $output = [];
        foreach($qb as $result){
            $output[$result->getStartTime()->format('d-m-Y')] = $result;
        }
        return $output;
    } 
    
    public function hasUserWorkedOn($user, $date)
    {
        $timesheet = (new Timesheet())
            ->setUser($user)
            ->setStartTime($date->setTime(0, 0, 0))
            ->setEndTime($date->setTime(23, 59, 59));
        return $this->getAlreadyWorkedToday($timesheet);
    }

    public function getAlreadyWorkedToday(Timesheet $timesheet)
    {
        $start = clone($timesheet->getStartTime());
        $start->setTime(0, 0, 0);
        $end = clone($timesheet->getEndTime());
        $end->setTime(23, 59, 59);
        $qb = $this->createQueryBuilder('t')
            ->select('Count(t.id) as total')
            ->where('t.user = :user')
            ->andWhere('t.id != :id')
            ->setParameter('id', $timesheet->getId()??0)
            ->andWhere('t.startTime >= :start')
            ->andWhere('t.endTime <= :end')
            ->setParameter('user', $timesheet->getUser())
            ->setParameter('start',$start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
        return $qb??0;
    }

    public function stopWorking(User $user)
    {
        $qb = $this->createQueryBuilder('t')
            ->update()
            ->set('t.endTime', ':end')
            ->where('t.user = :user')
            ->andWhere('t.endTime IS NULL')
            ->setParameter('user', $user)
            ->setParameter('end', new \DateTime())
            ->getQuery()
            ->execute();
        return $qb;
    }
    public function getWorkedHoursToday($user)
    {
        $timesheets =  $this->createQueryBuilder('t')
            ->andWhere('t.startTime >= :today')
            ->andWhere('t.user = :user')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        $output = [];
        foreach($timesheets as $timesheet){
            $key = $this->retrieveProjectFromTimesheet($timesheet->getRelatedCommentable());
            if(!isset($output[$key])){
                $output[$key] = 0;
            }
            $output[$key] += $timesheet->getWorkingMinutes();
        }
        return $output;
    }

    public function retrieveProjectFromTimesheet(?Commentable $commentable):?string
    {
        if($commentable == null){
            return null;
        }
        if(method_exists($commentable, 'getProject') && $commentable->getProject() !== null){
            return $commentable->getProject()->__toString();
        }
        return $commentable->__toString();

    }


    public function getWeekTimesheet(User $user)
    {
        $start = new \DateTime('monday this week');
        $end = new \DateTime('sunday this week');
        $output = [];
        $results = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.startTime >= :start')
            ->andWhere('t.endTime <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        foreach($results as $result){
            $date = new \DateTime($result->getStartTime()->format('l'));
            if(!isset($output[$date->format('l')])){
                $output[$date->format('l')] = 0;
            }
            $output[$date->format('l')] += $result->getWorkingMinutes();
        }
        return $output;
    }

    public function getTimesheetsForUserBetweenStartAndEnd(User $user, \DateTime $start, \DateTime $end)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.startTime >= :start')
            ->andWhere('t.endTime <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }


}
