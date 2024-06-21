<?php

namespace App\Repository;

use App\Entity\Timesheet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Prolyfix\SymfonyDatatablesBundle\Controller\DatatablesController;
use Prolyfix\SymfonyDatatablesBundle\Repository\DatatablesTrait;

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
    use DatatablesTrait;
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
    public function findDatatables(array $params): array {
        $qb = $this->createQueryBuilder('r');
        $relation = [];
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'user':
                    if(!in_array('ROLE_ADMIN', $value->getRoles())){
                        $qb->join('r.user', 'u');
                        $qb->andWhere('u.id = :user')
                                ->setParameter('user', $value);
    
                    }
                    break;
                case 'start':
                    break;
                case 'end':
                    break;
                case 'length':
                    break;
                case 'search':
                    break;
                case 'order':
                    $qb = $this->sortQueryAction($qb, $value, []);
                    break;
                default:
                    if($value == DatatablesController::ISNOTNULL){
                        $qb->andWHere('r.' . $key . ' IS NOT NULL');
                    }
                    elseif($value == null){
                        $qb->andWHere('r.' . $key . ' IS NULL');
                    }
                    elseif (is_array($value)) {
                        $qb->andWhere('r.' . $key . ' in (:' . $key . ')')
                                ->setParameter($key, $value);
                    }
                    else {
                        if ($value != "null" && strlen($value) > 0) {

                            $relations = explode(".", $key);
                            $countRelations = count($relations);
                            $ref = 'r';
                            for ($ii = 0; ($ii + 1) < $countRelations; $ii++) {
                                if (!in_array($relations[$ii], $this->alreadyJoin) && (!in_array($relations[$ii], $relation))) {
                                    $qb->join($ref . "." . $relations[$ii], $relations[$ii]);

                                    $relation[] = $relations[$ii];
                                    $this->alreadyJoin[] = $relations[$ii];
                                }
                                $ref = $relations[$ii];
                            }
                            $temp = uniqid();
                            $qb->andWhere($ref . "." . $relations[$ii] . " LIKE :" . str_replace(".", "", $key))
                                    ->setParameter(str_replace(".", "", $key), "".$value."");
                        }
                    }
                    break;
            }
        }
        $count = $this->returnCount(clone($qb));

        if (array_key_exists("length", $params)) {
            $qb->setMaxResults($params['length']);
        }
        if (array_key_exists("start", $params)) {
            $qb->setFirstResult($params['start']);
        }
        $entities = $qb->getQuery()
                ->getResult();
        return['count' => $count, 'results' => $entities];
    }

}
