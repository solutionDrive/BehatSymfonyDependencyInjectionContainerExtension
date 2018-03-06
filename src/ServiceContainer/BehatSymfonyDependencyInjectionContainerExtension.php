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
use FriendsOfBehat\CrossContainerExtension\ContainerBasedContainerAccessor;
use FriendsOfBehat\CrossContainerExtension\KernelBasedContainerAccessor;
use FriendsOfBehat\CrossContainerExtension\ServiceContainer\CrossContainerExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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
        foreach($config['identifiers'] as $identifier => $data) {
            $this->createIdentifierContainer($container, $config, $data, $identifier);
        }
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

    private function createIdentifierContainer(ContainerBuilder $container, array $config, $data, $identifier): void
    {
        $additionalContainer = new ContainerBuilder();
        $additionalContainer->setParameter('paths.base', $container->getParameter('paths.base'));
        $loader = $this->createLoader($additionalContainer, $config);

        foreach ($data['imports'] as $file) {
            $loader->load($file);
        }

        $container->merge($additionalContainer);

        if (null !== $this->crossContainerProcessor) {
            $this->crossContainerProcessor->addContainerAccessor(
                $identifier,
                new ContainerBasedContainerAccessor($additionalContainer)
            );
        }
    }

    private function createLoader(ContainerBuilder $container, array $config): LoaderInterface
    {
        $fileLocator = new FileLocator($container->getParameter('paths.base'));

        return new DelegatingLoader(new LoaderResolver([
            new XmlFileLoader($container, $fileLocator),
            new YamlFileLoader($container, $fileLocator),
            new PhpFileLoader($container, $fileLocator),
        ]));
    }
}

