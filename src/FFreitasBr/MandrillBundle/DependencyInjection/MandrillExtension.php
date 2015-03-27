<?php

namespace FFreitasBr\MandrillBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class MandrillExtension
 *
 * @package FFreitasBr\MandrillBundle\DependencyInjection
 */
class MandrillExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configurations
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // save configurations in container
        $container->setParameter('mandrill.configuration', $config);

        // load services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // load dispatchers
        $this->loadDispatchers($config, $container);

//        $container->setParameter('hip_mandrill.api_key', $config['api_key']);
//        $container->setParameter('hip_mandrill.disable_delivery', $config['disable_delivery']);
//        $container->setParameter('hip_mandrill.default.sender', $config['default']['sender']);
//        $container->setParameter('hip_mandrill.default.sender_name', $config['default']['sender_name']);
//        $container->setParameter('hip_mandrill.default.subaccount', $config['default']['subaccount']);
//        $container->setParameter('hip_mandrill.proxy', $config['proxy']);

    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function loadDispatchers(array $config, ContainerBuilder $container)
    {
        foreach ($config['dispatchers'] as $dispatcherName => $dispatcherConfig) {
            $this->registerDispatcher($container, $dispatcherName, $dispatcherConfig);
        }
        $container->setAlias('mandrill.dispatcher', 'mandrill.dispatcher.default');
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $dispatcherName
     * @param                  $dispatcherConfig
     */
    protected function registerDispatcher(ContainerBuilder $container, $dispatcherName, $dispatcherConfig)
    {
        $container
            ->setDefinition(
                sprintf('mandrill.dispatcher.%s', $dispatcherName),
                new DefinitionDecorator('mandrill.dispatcher.abstract')
            )
            ->setArguments(
                array(
                    new Reference('service_container'),
                    new Parameter('mandrill.client.class'),
                    $dispatcherName,
                    $dispatcherConfig
                )
            )
        ;
    }
}
