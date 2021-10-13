<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Admin\Repository;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\User\Entities\User;
use Dravencms\Database\EntityManager;


class MenuRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Menu|string */
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
     * @return \Doctrine\Persistence\ObjectRepository|Menu|string
     */
    public function getMenuRepository()
    {
        return $this->menuRepository;
    }

    /**
     * @param array $options
     * @param User $user
     * @return mixed
     */
    public function getHtmlTreeForUser(array $options, User $user)
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
    public function deleteItem(int $id): void
    {
        $menu = $this->menuRepository->find($id);
        $this->entityManager->remove($menu);
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     * @return Menu|null
     */
    public function getOneById(int $id): ?Menu
    {
        return $this->menuRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param string $name
     * @return Menu|null
     */
    public function getOneByName(string $name): ?Menu
    {
        return $this->menuRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param string $presenter
     * @param string $action
     * @return Menu|null
     */
    public function getByPresenterAndAction(string $presenter, string $action = 'default'): ?Menu
    {
        return $this->menuRepository->findOneBy(['presenter' => $presenter, 'action' => $action]);
    }

    /**
     * @param User $user
     * @return mixed
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
    private function buildParentTreeResolver(Menu $menu): array
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
    public function buildParentTree(Menu $menu): array
    {
        return array_reverse($this->buildParentTreeResolver($menu));
    }

    /**
     * @param string $presenter
     * @return Menu|null
     */
    public function getOneByPresenter(string $presenter): ?Menu
    {
        return $this->menuRepository->findOneBy(['presenter' => $presenter]);
    }
}