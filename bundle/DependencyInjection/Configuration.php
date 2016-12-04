<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * Constructor.
     *
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $rootNode->children()
            ->arrayNode('item_types')
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('sections')
                        ->performNoDeepMerging()
                        ->prototype('scalar')->end()
                    ->end()
                    ->integerNode('min_selected')
                        ->treatNullLike(0)
                        ->defaultValue(1)
                        ->min(0)
                    ->end()
                    ->integerNode('max_selected')
                        ->treatNullLike(0)
                        ->defaultValue(0)
                        ->min(0)
                    ->end()
                    ->arrayNode('tree')
                        ->addDefaultsIfNotSet()
                        ->canBeDisabled()
                    ->end()
                    ->arrayNode('search')
                        ->addDefaultsIfNotSet()
                        ->canBeDisabled()
                    ->end()
                    ->arrayNode('preview')
                        ->validate()
                            ->always(function ($v) {
                                if ($v['enabled'] && !isset($v['template'])) {
                                    throw new InvalidConfigurationException('When preview is enabled, template needs to be specified');
                                }

                                return $v;
                            })
                        ->end()
                        ->isRequired()
                        ->canBeDisabled()
                        ->children()
                            ->scalarNode('template')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('columns')
                        ->validate()
                            ->always(function ($v) {
                                if (!isset($v['name'])) {
                                    throw new InvalidConfigurationException('Column with "name" identifier is required');
                                }

                                return $v;
                            })
                        ->end()
                        ->performNoDeepMerging()
                        ->prototype('array')
                            ->validate()
                                ->always(function ($v) {
                                    $exception = new InvalidConfigurationException('Column specification needs to have either "template" or "value_provider" keys');

                                    if (isset($v['template']) && isset($v['value_provider'])) {
                                        throw $exception;
                                    }

                                    if (!isset($v['template']) && !isset($v['value_provider'])) {
                                        throw $exception;
                                    }

                                    return $v;
                                })
                            ->end()
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('template')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('value_provider')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('default_columns')
                        ->performNoDeepMerging()
                        ->prototype('scalar')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}