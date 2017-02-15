<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Repository;

use Dravencms\Model\Admin\Entities\Notification;
use Dravencms\Model\Admin\Entities\NotificationViewed;
use Dravencms\Model\User\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette;

class NotificationViewedRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
     * @param $id
     * @return null|NotificationViewed
     */
    public function getOneById($id)
    {
        return $this->notificationViewedRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param Notification $notification
     * @param User $user
     * @return ull|NotificationViewed
     */
    public function getOneByNotificationAndUser(Notification $notification, User $user)
    {
        return $this->notificationViewedRepository->findOneBy(['notification' => $notification, 'user' => $user]);
    }
}