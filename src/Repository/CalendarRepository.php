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
    public function retrieveHolidaysForGroupForYear($group, $year)
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
        dump($output);
        return $output;
    }


    public function retrieveHolidaysForGroup($group,$start,$end)
    {
        dump($group,$start,$end);
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.workingGroup = :group')
            ->andWhere('c.startDate <= :end AND c.endDate >= :start')
            ->setParameter('group', $group)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        $output = [];
        $di = new \DateInterval('P1D');
        dump($query);
        foreach($query as $result){
            dump("ici");
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
    public function getGroupHolidays(\DateTime $start, \DateTime $end, WorkingGroup $group)
    {
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
