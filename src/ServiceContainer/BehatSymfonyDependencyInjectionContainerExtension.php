<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace solutionDrive\BehatSymfonyDependencyInjectionContainerExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\CrossContainerExtension\ServiceContainer\CrossContainerExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BehatSymfonyDependencyInjectionContainerExtension implements Extension
{
    /**
     * @var CompilerPassInterface|null
     */
    private $crossContainerProcessor;

    /**
     * {@inheritdoc}
     */
    public function getConfigKey(): string
    {
        return 'sd_behat_symfony_dependency_injection_container';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager): void
    {
        /** @var CrossContainerExtension $extension */
        $extension = $extensionManager->getExtension('fob_cross_container');

        if (null !== $extension) {
            $this->crossContainerProcessor = $extension->getCrossContainerProcessor();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->arrayNode('identifiers')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('imports')
                                ->performNoDeepMerging()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config): void
    {
        // TODO: Implement load() method.
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (null !== $this->crossContainerProcessor) {
            $this->crossContainerProcessor->process($container);
        }
    }
}

