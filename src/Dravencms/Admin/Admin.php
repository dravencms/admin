<?php

namespace Dravencms\Admin;
use Dravencms\Model\Admin\Entities\Notification;
use Dravencms\Model\Admin\Repository\NotificationRepository;
use Dravencms\Model\User\Entities\User;
use Dravencms\Model\User\Entities\AclResource;
use Kdyby\Doctrine\EntityManager;
use Nette\SmartObject;

/**
 * Class Admin
 * @package Dravencms\Admin
 */
class Admin
{
    use SmartObject;
    
    /** @var NotificationRepository */
    private $notificationRepository;

    /** @var EntityManager  */
    private $entityManager;

    /**
     * Admin constructor.
     * @param NotificationRepository $notificationRepository
     * @param EntityManager $entityManager
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        EntityManager $entityManager
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $name
     * @param $description
     * @param $icon
     * @param string $type
     * @param null $url
     * @param array $urlArguments
     * @param User|null $user
     * @param AclResource|null $aclResource
     * @return Notification
     * @throws \Exception
     */
    public function addNotification($name, $description, $icon, $type = Notification::TYPE_DEFAULT, $url = null, array $urlArguments = [], User $user = null, AclResource $aclResource = null)
    {
        $notification = new Notification($name, $description, $icon, $type, $url, $urlArguments, $user, $aclResource);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }
}
