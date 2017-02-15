<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Repository;

use Dravencms\Model\Admin\Entities\Notification;
use Dravencms\Model\User\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Doctrine\ORM\Query\Expr;
use Nette;

class NotificationRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $notificationRepository;

    /** @var EntityManager */
    private $entityManager;
    
    /**
     * NotificationRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $entityManager->getRepository(Notification::class);
    }

    /**
     * @param $id
     * @return null|Notification
     */
    public function getOneById($id)
    {
        return $this->notificationRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param User $user
     * @return Notification[]
     */
    public function getForUser(User $user)
    {
        $qb = $this->notificationRepository->createQueryBuilder('n');

        $query  = $qb->select('n')
            ->leftJoin('n.aclOperation', 'ao')
            ->leftJoin('ao.groups', 'g')
            ->leftJoin('g.users', 'gu')
            ->leftJoin('n.notificationsViewed', 'nv', Expr\Join::WITH, 'nv.user = :user')
            ->orderBy('n.createdAt', 'DESC')
            ->where('gu.id = :user AND n.user IS NULL AND nv.id IS NULL')

            ->orWhere('n.user = :user AND gu.id IS NULL AND nv.id IS NULL')

            ->orWhere('n.user = :user AND gu.id = :user AND nv.id IS NULL')

            ->orWhere('gu.id IS NULL AND n.user IS NULL AND nv.id IS NULL')

            ->groupBy('n')
            ->setParameters(
                [
                    'user' => $user
                ]
            )
            ->getQuery();

        return $query->getResult();
    }
    
}