<?php

namespace FFreitasBr\MandrillBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package FFreitasBr\MandrillBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mandrill');

        $this->addDispatchersSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Add Adapters section to configuration tree
     *
     * @param ArrayNodeDefinition $node
     */
    protected function addDispatchersSection(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('dispatcher')
            ->beforeNormalization()
                ->ifTrue(
                    function ($v) {
                        return is_array($v) && !array_key_exists('dispatchers', $v) && array_key_exists('api_key', $v);
                    }
                )
                ->then(
                    function ($v) {
                        return array(
                            'dispatchers' => $v
                        );
                    }
                )
            ->end()
            ->children()
            ->arrayNode('dispatchers')
                ->isRequired()
                ->beforeNormalization()
                    ->ifTrue(
                        function ($v) {
                            return is_array($v) && !array_key_exists('default', $v) && array_key_exists('api_key', $v);
                        }
                    )
                    ->then(
                        function ($v) {
                            return array(
                                'default' => $v
                            );
                        }
                    )
                ->end()
                ->beforeNormalization()
                    ->always(
                        function ($v) {
//                            THIS CODE PROPAGATE EMPTY PROPERTIES OF THE DEFAULT DISPATCHER CONFIGURATION TO OTHER DISPATCHERS
//                            foreach ($v as $key => $value) {
//                                if ($key === 'default') {
//                                    continue;
//                                }
//                                $v[$key] = array_replace_recursive($v['default'], $value);
//                            }
                            return $v;
                        }
                    )
                ->end()
                ->beforeNormalization()
                    ->always(
                        function ($v) {
                            foreach ($v as $dispatcherName => &$dispatcherConfig) {
                                if (!isset($dispatcherConfig['defaults']) || !is_array($dispatcherConfig['defaults'])) {
                                    $dispatcherConfig['defaults'] = array();
                                }
                            }
                            return $v;
                        }
                    )
                ->end()
                ->prototype('array')
                ->children()
                    ->scalarNode('api_key')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->cannotBeOverwritten()
                    ->end()
                    ->booleanNode('disable_delivery')
                        ->defaultFalse()
                    ->end()
                    ->arrayNode('proxy')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('use')
                                ->defaultFalse()
                            ->end()
                            ->scalarNode('host')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('port')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('user')
                                ->defaultNull()
                            ->end()
                            ->scalarNode('password')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('defaults')
                        ->isRequired()
                        ->children()
                            ->scalarNode('sender')->defaultNull()->end()
                            ->scalarNode('sender_name')->defaultNull()->end()
                            ->scalarNode('subaccount')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
