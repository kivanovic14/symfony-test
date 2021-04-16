<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findUnseenByUser(User $user)
        // this func fetch the count of notifications that were unseen by a user
    {
        $qb = $this->createQueryBuilder('n'); // n = notification
        return $qb->select('count(n)') // return count of te not record
            ->where('n.user = :user') // user is the user that actually will get the notification
            ->andWhere('n.seen = 0')
            ->setParameter('user',$user)
            ->getQuery()
            ->getSingleScalarResult(); // this method returns integer

    }

    public function markAllAsReadByUser(User $user)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->update('App\Entity\Notification', 'n')
            ->set('n.seen', true)
            ->where('n.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();


    }
}
