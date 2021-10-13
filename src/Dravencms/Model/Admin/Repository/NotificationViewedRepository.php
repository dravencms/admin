<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Repository;

use Dravencms\Model\Admin\Entities\Notification;
use Dravencms\Model\Admin\Entities\NotificationViewed;
use Dravencms\Model\User\Entities\User;
use Dravencms\Database\EntityManager;


class NotificationViewedRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|NotificationViewed|string */
    private $notificationViewedRepository;

    /** @var EntityManager */
    private $entityManager;
    
    /**
     * NotificationRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->notificationViewedRepository = $entityManager->getRepository(NotificationViewed::class);
    }

    /**
     * @param int $id
     * @return NotificationViewed|null
     */
    public function getOneById(int $id): ?NotificationViewed
    {
        return $this->notificationViewedRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param Notification $notification
     * @param User $user
     * @return NotificationViewed|null
     */
    public function getOneByNotificationAndUser(Notification $notification, User $user): ?NotificationViewed
    {
        return $this->notificationViewedRepository->findOneBy(['notification' => $notification, 'user' => $user]);
    }
}