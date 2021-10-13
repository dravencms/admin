<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\NotificationModule;

use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Admin\Entities\NotificationViewed;
use Dravencms\Model\Admin\Repository\NotificationRepository;
use Dravencms\Model\Admin\Repository\NotificationViewedRepository;
use Dravencms\Database\EntityManager;


/**
 * Homepage presenter.
 */
class NotificationPresenter extends SecuredPresenter
{
    /** @var NotificationViewedRepository @inject */
    public $notificationViewedRepository;

    /** @var NotificationRepository @inject */
    public $notificationRepository;

    /** @var EntityManager @inject */
    public $entityManager;


    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\BadRequestException
     */
    public function renderUrl(int $id): void
    {
        $notification = $this->notificationRepository->getOneById($id);
        if (!$notification || !$notification->getUrl())
        {
            $this->error();
        }

        if (!$this->notificationViewedRepository->getOneByNotificationAndUser($notification, $this->getUserEntity()))
        {
            $notificationViewed = new NotificationViewed($notification, $this->getUserEntity());
            $this->entityManager->persist($notificationViewed);
            $this->entityManager->flush();
        }

        $this->redirect($notification->getUrl(), $notification->getUrlArguments());
    }
}
