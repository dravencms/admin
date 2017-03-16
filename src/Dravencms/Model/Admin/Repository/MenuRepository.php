<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Repository;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\User\Entities\User;
use Kdyby\Doctrine\EntityManager;
use Nette;

class MenuRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $menuRepository;

    /** @var EntityManager */
    private $entityManager;
    
    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->menuRepository = $entityManager->getRepository(Menu::class);
    }

    /**
     * @return \Kdyby\Doctrine\EntityRepository
     */
    public function getMenuRepository()
    {
        return $this->menuRepository;
    }

    /**
     * @param $options
     * @param User $user
     * @return mixed
     */
    public function getHtmlTreeForUser($options, User $user)
    {
        $query = $this->menuRepository
            ->createQueryBuilder('node')
            ->select('node')
            ->join('node.aclOperation', 'ao')
            ->join('ao.groups', 'g')
            ->join('g.users', 'gu')
            ->orderBy('node.lft', 'ASC')
            ->where('node.isActive = :isActive')
            ->andWhere('gu = :user')
            ->groupBy('node')
            ->setParameters(
                [
                    'user' => $user,
                    'isActive' => true
                ]
            )
            ->getQuery();

        return $this->menuRepository->buildTree($query->getArrayResult(), $options);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function deleteItem($id)
    {
        $menu = $this->menuRepository->find($id);
        $this->entityManager->remove($menu);
        $this->entityManager->flush();
    }

    /**
     * @param $id
     * @return mixed|null|Menu
     */
    public function getById($id)
    {
        return $this->menuRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param $name
     * @return mixed|null|Menu
     */
    public function getOneByName($name)
    {
        return $this->menuRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param $presenter
     * @param string $action
     * @return mixed|null|Menu
     */
    public function getByPresenterAndAction($presenter, $action = 'default')
    {
        return $this->menuRepository->findOneBy(['presenter' => $presenter, 'action' => $action]);
    }

    /**
     * @param $presenter
     * @return mixed|null|Menu
     */
    public function getByPresenter($presenter)
    {
        return $this->menuRepository->findOneBy(['presenter' => $presenter]);
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHomePageForUser(User $user)
    {
        $qb = $this->menuRepository->createQueryBuilder('m')
            ->select('m')
            ->join('m.aclOperation', 'ao')
            ->join('ao.groups', 'g')
            ->join('g.users', 'gu')
            ->where('gu = :user')
            ->andWhere('m.isHomePage = :isHomePage')
            ->setParameters([
                'user' => $user,
                'isHomePage' => true
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Menu $menu
     * @return Menu[]
     */
    private function buildParentTreeResolver(Menu $menu)
    {
        $breadcrumb = [];

        $breadcrumb[] = $menu;

        if ($menu->getParent()) {
            foreach ($this->buildParentTreeResolver($menu->getParent()) AS $sub) {
                $breadcrumb[] = $sub;
            }
        }
        return $breadcrumb;
    }

    /**
     * @param Menu $menu
     * @return Menu[]
     */
    public function buildParentTree(Menu $menu)
    {
        return array_reverse($this->buildParentTreeResolver($menu));
    }

    /**
     * @param $presenter
     * @return mixed|null|Menu
     */
    public function getOneByPresenter($presenter)
    {
        return $this->menuRepository->findOneBy(['presenter' => $presenter]);
    }
}