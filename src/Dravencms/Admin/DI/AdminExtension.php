<?php

namespace Dravencms\Admin\DI;

use Kdyby\Console\DI\ConsoleExtension;
use Kdyby\Translation\DI\ITranslationProvider;
use Nette;
use Nette\DI\Compiler;
use Nette\DI\Configurator;
/**
 * Class AdminExtension
 * @package Dravencms\Admin\DI
 */
class AdminExtension extends Nette\DI\CompilerExtension implements ITranslationProvider
{
    public function getTranslationResources()
    {
        return [__DIR__.'/../lang'];
    }

    public function loadConfiguration()
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();


        $builder->addDefinition($this->prefix('admin'))
            ->setClass('Dravencms\Admin\Admin', []);

        $builder->addDefinition($this->prefix('notifications'))
            ->setClass('Dravencms\Admin\Notifications', []);

        $builder->addDefinition($this->prefix('adminFiltersLatte'))->setClass('Dravencms\Admin\Filters\Latte');

        $this->loadComponents();
        $this->loadModels();
        $this->loadConsole();
    }


    /**
     * @param Configurator $config
     * @param string $extensionName
     */
    public static function register(Configurator $config, $extensionName = 'adminExtension')
    {
        $config->onCompile[] = function (Configurator $config, Compiler $compiler) use ($extensionName) {
            $compiler->addExtension($extensionName, new AdminExtension());
        };
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $registerToLatte = function (Nette\DI\ServiceDefinition $def) {
            $def->addSetup('addFilter', ['formatCounter', [$this->prefix('@adminFiltersLatte'), 'formatCounter']]);
        };

        $latteFactoryService = $builder->getByType('Nette\Bridges\ApplicationLatte\ILatteFactory');
        if (!$latteFactoryService || !self::isOfType($builder->getDefinition($latteFactoryService)->getClass(), 'Latte\engine')) {
            $latteFactoryService = 'nette.latteFactory';
        }

        if ($builder->hasDefinition($latteFactoryService) && self::isOfType($builder->getDefinition($latteFactoryService)->getClass(), 'Latte\Engine')) {
            $registerToLatte($builder->getDefinition($latteFactoryService));
        }

        if ($builder->hasDefinition('nette.latte')) {
            $registerToLatte($builder->getDefinition('nette.latte'));
        }

        $notification = $builder->getDefinition($this->prefix('notifications'));


        foreach ($builder->findByType('Dravencms\Admin\INotificationArea') AS $serviceName => $service) {
            $notification->addSetup('addNotificationAreaProvider', ['@' . $serviceName]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(array $defaults = [], $expand = true)
    {
        $defaults = [
        ];

        return parent::getConfig($defaults, $expand);
    }

    protected function loadComponents()
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/components.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('components.' . $i))
                ->setInject(FALSE); // lazy injects
            if (is_string($command)) {
                $cli->setImplement($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadModels()
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/models.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('models.' . $i))
                ->setInject(FALSE); // lazy injects
            if (is_string($command)) {
                $cli->setClass($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    protected function loadConsole()
    {
        $builder = $this->getContainerBuilder();

        foreach ($this->loadFromFile(__DIR__ . '/console.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('cli.' . $i))
                ->addTag(ConsoleExtension::TAG_COMMAND)
                ->setInject(FALSE); // lazy injects

            if (is_string($command)) {
                $cli->setClass($command);

            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

    /**
     * @param string $class
     * @param string $type
     * @return bool
     */
    private static function isOfType($class, $type)
    {
        return $class === $type || is_subclass_of($class, $type);
    }
}
