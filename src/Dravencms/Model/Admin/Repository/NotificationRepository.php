<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Repository;

use Dravencms\Model\Admin\Entities\Notification;
use Dravencms\Model\User\Entities\User;
use Kdyby\Doctrine\EntityManager;
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

    
}