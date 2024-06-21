<?php

namespace App\Repository;

use App\Entity\Calendar;
use App\Entity\WorkingGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Prolyfix\SymfonyDatatablesBundle\Repository\DatatablesTrait;

/**
 * @extends ServiceEntityRepository<Calendar>
 *
 * @method Calendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendar[]    findAll()
 * @method Calendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarRepository extends ServiceEntityRepository
{
    use DatatablesTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    public function getCalendarsForYear($year)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.startDate BETWEEN :start AND :end')
            ->orWhere('c.endDate BETWEEN :start AND :end')
            ->setParameter('start', $year . '-01-01')
            ->setParameter('end', $year . '-12-31')
            ->getQuery();

        return $query->getResult();
    }

    public function retrieveToilDaysAfter(\DateTime $date)
    {
        dump($date);
        $now = new \DateTime();
        $query = $this->createQueryBuilder('c')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isTimeHoliday = 1')
            ->where('c.startDate >= :date')
            ->setParameter('date', $date)
            ->andWhere('c.startDate <= :now')
            ->setParameter('now', $now)
            ->getQuery();

        return $query->getResult();
    }

    public function hasHoliday($user, $date)
    {
        dump("1");
        $date->setTime(0, 0, 0);
        if(($user->getStartDate() > $date || ($user->getEndDate()!==null && $user->getEndDate() < $date))&&$user->getStartDate()!== null) return false;
        dump("1");
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user')
            ->andWhere('c.startDate <= :date AND c.endDate >= :date')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isHoliday = 1')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->getQuery();

        if ($query->getSingleScalarResult() > 0) return true;
        dump("1");

        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user')
            ->andWhere('c.startDate <= :date AND c.endDate >= :date')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isBankHoliday = 1')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->getQuery();
        
        if ($query->getSingleScalarResult() > 0) return true;
        dump("3");

        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.workingGroup = :group')
            ->andWhere('c.startDate <= :date AND c.endDate >= :date')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isHoliday = 1')
            ->setParameter('group', $user->getWorkingGroup())
            ->setParameter('date', $date)
            ->getQuery();
        
        if ($query->getSingleScalarResult() > 0) return true;
        dump("1");

        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.workingGroup = :group')
            ->andWhere('c.startDate <= :date AND c.endDate >= :date')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isBankHoliday = 1')
            ->setParameter('group', $user->getWorkingGroup())
            ->setParameter('date', $date)
            ->getQuery();
        
        if ($query->getSingleScalarResult() > 0) return true;
        dump("1");

        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.startDate <= :date AND c.endDate >= :date')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isBankHoliday = 1')
            ->setParameter('date', $date)
            ->getQuery();
        
        if ($query->getSingleScalarResult() > 0) return true;
        dump("6");

        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.startDate <= :date AND c.endDate >= :date')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isHoliday = 1')
            ->setParameter('date', $date)
            ->getQuery();
        
        if ($query->getSingleScalarResult() > 0) return true;
        dump("7");

        return false;

    }

    public function retrieveHolidayForYear($user, $year)
    {
        $query = $this->createQueryBuilder('c')
            ->select('SUM(c.absenceInWorkingDays) as total')
            ->where('c.user = :user')
            ->andWhere('c.startDate BETWEEN :start AND :end')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isHoliday = 1')
            ->setParameter('user', $user)
            ->setParameter('start', $year . '-01-01')
            ->setParameter('end', $year . '-12-31')
            ->getQuery();

        return $query->getSingleScalarResult();
    }
    public function retrieveHolidaysForGroupForYear( $group, $year)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.workingGroup = :group')
            ->andWhere('c.startDate BETWEEN :start AND :end')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isHoliday = 1')
            ->setParameter('group', $group)
            ->setParameter('start', $year . '-01-01')
            ->setParameter('end', $year . '-12-31')
            ->getQuery();

        return $query->getResult();
    }

    public function retrieveHolidaysForFirmForYear($year)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.workingGroup IS NULL')
            ->andWhere('c.user IS NULL')
            ->andWhere('c.startDate BETWEEN :start AND :end')
            ->setParameter('start', $year . '-01-01')
            ->setParameter('end', $year . '-12-31')
            ->getQuery();

        return $query->getResult();
    }
    public function retrieveBankHolidaysForYear($year)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.workingGroup IS NULL')
            ->andWhere('c.user IS NULL')
            ->andWhere('c.startDate BETWEEN :start AND :end')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isBankHoliday = 1')
            ->setParameter('start', $year . '-01-01')
            ->setParameter('end', $year . '-12-31')
            ->getQuery();
        $output = array();
        foreach($query->getResult() as $result){
            $output[$result->getStartDate()->format('d-m-Y')] = $result;
        }

        return $output;
    }


    public function retrieveHolidaysForUser($user,$start,$end)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.user = :user')
            ->andWhere('c.startDate BETWEEN :start AND :end')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isHoliday = 1')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        $output = [];
        $di = new \DateInterval('P1D');
        
        foreach($query as $result){
            $start2 = clone $result->getStartDate();
            while($start2 <= $result->getEndDate()){
                $output[$start2->format('d-m-Y')] = $result;
                $start2->add($di);
            }
        }
        return $output;
    }


    public function retrieveHolidaysForGroup($group,$start,$end)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.workingGroup = :group')
            ->andWhere('c.startDate <= :end AND c.endDate >= :start')
            ->andWhere('c.user IS NULL')
            ->setParameter('group', $group)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        $output = [];
        $di = new \DateInterval('P1D');
        foreach($query as $result){
            $start2 = clone $result->getStartDate();
            while($start2 <= $result->getEndDate()){
                $output[$start2->format('d-m-Y')] = $result;
                $start2->add($di);
            }
        }
    return $output;
    }

    public function findOpenRequests($users){
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.user IN (:users)')
            ->andWhere('c.state = :state')
            ->setParameter('users', $users)
            ->setParameter('state', Calendar::STATE_PENDING)
            ->getQuery();

        return $query->getResult();
    }
    public function getBankHolidays(\DateTime $start, \DateTime $end)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.startDate BETWEEN :start AND :end')
            ->join('c.typeOfAbsence', 't')
            ->andWhere('t.isBankHoliday = 1')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $query->getResult();
    }
    public function getGroupHolidays(\DateTime $start, \DateTime $end, ?WorkingGroup $group)
    {
        if($group == null) return [];
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.startDate BETWEEN :start AND :end')
            ->andWhere('c.workingGroup = :group')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('group', $group)
            ->getQuery();

        return $query->getResult();
    }
    public function calculatePendingForYear($user,$year){
        $query = $this->createQueryBuilder('c')
            ->select('SUM(c.absenceInWorkingDays) as total')
            ->where('c.user = :user')
            ->andWhere('c.startDate BETWEEN :start AND :end')
            ->andWhere('c.state = :state')
            ->setParameter('user', $user)
            ->setParameter('start', $year . '-01-01')
            ->setParameter('end', $year . '-12-31')
            ->setParameter('state', Calendar::STATE_PENDING)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
    public function hasAlreadyHolidayBookedDuringPeriod(Calendar $calendar)
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user')
            ->andWhere('c.startDate <= :start AND c.endDate >= :start')
            ->setParameter('user', $calendar->getUser())
            ->setParameter('start', $calendar->getStartDate())
            ->getQuery();

        return $query->getSingleScalarResult() > 0;
    }

}
