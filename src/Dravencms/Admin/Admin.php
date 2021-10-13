<?php declare(strict_types = 1);

namespace Dravencms\Admin;
use Dravencms\Model\Admin\Entities\Notification;
use Dravencms\Model\Admin\Repository\NotificationRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\User;
use Dravencms\Database\EntityManager;
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
     * @param string $name
     * @param string $description
     * @param string $icon
     * @param string $type
     * @param string|null $url
     * @param array $urlArguments
     * @param User|null $user
     * @param AclOperation|null $aclOperation
     * @return Notification
     */
    public function addNotification(string $name, string $description, string $icon, $type = Notification::TYPE_DEFAULT, string $url = null, array $urlArguments = [], User $user = null, AclOperation $aclOperation = null)
    {
        $notification = new Notification($name, $description, $icon, $type, $url, $urlArguments, $user, $aclOperation);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }
}
