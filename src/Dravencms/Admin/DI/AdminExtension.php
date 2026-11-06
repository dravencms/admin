<?php declare(strict_types = 1);

namespace Dravencms\Admin\DI;

use Contributte\Translation\DI\TranslationProviderInterface;
use Dravencms\Admin\Admin;
use Dravencms\Admin\Filters\Latte;
use Dravencms\Admin\INotificationArea;
use Dravencms\Admin\Notifications;
use Dravencms\Security\Authenticator;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;

/**
 * Class AdminExtension
 * @package Dravencms\Admin\DI
 */
class AdminExtension extends CompilerExtension implements TranslationProviderInterface
{
    public function getConfigSchema(): \Nette\Schema\Schema
    {
        return \Nette\Schema\Expect::structure([
            'authRateLimitDirectory' => \Nette\Schema\Expect::string()->default($this->getContainerBuilder()->parameters['tempDir'] . '/admin-auth-limits'),
            'menu' => \Nette\Schema\Expect::structure([
                'homepage' => \Nette\Schema\Expect::string()->nullable()->default(null),
                'overrides' => \Nette\Schema\Expect::array()->default([]),
            ]),
        ]);
    }

    public function getTranslationResources(): array
    {
        return [__DIR__.'/../lang'];
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('authLimiter'))->setFactory(\Dravencms\Admin\AuthRateLimiter::class, [$this->getConfig()->authRateLimitDirectory]);


        $builder->addDefinition($this->prefix('admin'))
            ->setFactory(Admin::class);

        $builder->addDefinition($this->prefix('notifications'))
            ->setFactory(Notifications::class);

        $builder->addDefinition($this->prefix('adminFiltersLatte'))
            ->setFactory(Latte::class);

        $builder->addDefinition($this->prefix('menuRegistry'))
            ->setFactory(\Dravencms\Admin\Menu\AdminMenuRegistry::class, [
                'overrides' => $this->getConfig()->menu->overrides,
                'homepage' => $this->getConfig()->menu->homepage,
            ]);
        $builder->addDefinition($this->prefix('dashboardRegistry'))->setFactory(\Dravencms\Admin\Dashboard\DashboardRegistry::class);
        $builder->addDefinition($this->prefix('systemDashboard'))->setFactory(\Dravencms\Admin\Dashboard\SystemComponent::class)->setAutowired(false);
        $builder->addDefinition($this->prefix('coreMenuProvider'))->setFactory(\Dravencms\Admin\Menu\CoreMenuProvider::class);

        $this->loadComponents();
        $this->loadModels();
        $this->loadConsole();
    }


    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $latteFactoryService = $builder->getDefinitionByType(LatteFactory::class);
        $latteFactoryService->getResultDefinition()->addSetup('addFilter', ['formatCounter', [$this->prefix('@adminFiltersLatte'), 'formatCounter']]);

        $menu = $builder->getDefinition($this->prefix('menuRegistry'));
        foreach ($builder->findByType(\Dravencms\Admin\Menu\AdminMenuProviderInterface::class) as $serviceName => $service) {
            $menu->addSetup('addProvider', ['@' . $serviceName]);
        }
        $menu->addSetup('getItems');

        $dashboard = $builder->getDefinition($this->prefix('dashboardRegistry'));
        foreach ($builder->findByType(\Dravencms\Admin\Dashboard\IDashboardComponent::class) as $serviceName => $service) {
            if (!is_a($service->getType(), \Nette\Application\UI\Control::class, true)) {
                throw new \LogicException('Dashboard component must extend Nette UI Control: ' . $serviceName);
            }
            $dashboard->addSetup('addFactory', [new \Nette\PhpGenerator\Literal('fn () => $this->createService(?)', [$serviceName])]);
        }

        $notification = $builder->getDefinition($this->prefix('notifications'));

        foreach ($builder->findByType(INotificationArea::class) AS $serviceName => $service) {
            $notification->addSetup('addNotificationAreaProvider', ['@' . $serviceName]);
        }
    }


    protected function loadComponents(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/components.neon') as $i => $command) {
            $cli = $builder->addFactoryDefinition($this->prefix('components.' . $i));
            if (is_string($command)) {
                $cli->setImplement($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadModels(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/models.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('models.' . $i));
            if (is_string($command)) {
                $cli->setFactory($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadConsole(): void
    {
        $builder = $this->getContainerBuilder();

        foreach ($this->loadFromFile(__DIR__ . '/console.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('cli.' . $i))
                ->setAutowired(false);

            if (is_string($command)) {
                $cli->setFactory($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }
}
