<?php

namespace Dravencms\AdminModule\NotificationModule;

use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Admin\Entities\NotificationViewed;
use Dravencms\Model\Admin\Repository\NotificationRepository;
use Dravencms\Model\Admin\Repository\NotificationViewedRepository;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Tracy\Debugger;

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

    public function renderDefault()
    {
    }

    /**
     * @param $id
     */
    public function renderUrl($id)
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
