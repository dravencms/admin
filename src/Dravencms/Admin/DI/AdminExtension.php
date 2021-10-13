<?php declare(strict_types = 1);

namespace Dravencms\Admin\DI;

use Contributte\Translation\DI\TranslationProviderInterface;
use Dravencms\Admin\Admin;
use Dravencms\Admin\Filters\Latte;
use Dravencms\Admin\INotificationArea;
use Dravencms\Admin\Notifications;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI\CompilerExtension;

/**
 * Class AdminExtension
 * @package Dravencms\Admin\DI
 */
class AdminExtension extends CompilerExtension implements TranslationProviderInterface
{
    public static $prefix = 'admin';

    public function getTranslationResources(): array
    {
        return [__DIR__.'/../lang'];
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();


        $builder->addDefinition($this->prefix(self::$prefix))
            ->setFactory(Admin::class, []);

        $builder->addDefinition($this->prefix(self::$prefix.'.notifications'))
            ->setFactory(Notifications::class, []);

        $builder->addDefinition($this->prefix(self::$prefix.'.adminFiltersLatte'))
            ->setFactory(Latte::class);

        $this->loadComponents();
        $this->loadModels();
        $this->loadConsole();
    }


    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $latteFactoryService = $builder->getByType(LatteFactory::class);
        $latteFactoryService->addSetup('addFilter', ['formatCounter', [$this->prefix('@'.self::$prefix.'adminFiltersLatte'), 'formatCounter']]);

        $notification = $builder->getDefinition($this->prefix(self::$prefix.'.notifications'));

        foreach ($builder->findByType(INotificationArea::class) AS $serviceName => $service) {
            $notification->addSetup('addNotificationAreaProvider', ['@' . $serviceName]);
        }
    }


    protected function loadComponents(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/components.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('components.' . $i))
                ->setAutowired(false);
            if (is_string($command)) {
                $cli->setFactory($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadModels(): void
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/models.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('models.' . $i))
                ->setAutowired(false);
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
