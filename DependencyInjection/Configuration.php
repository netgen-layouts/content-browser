<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
            ->arrayNode('items')
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('converter')
                        ->isRequired()
                    ->end()
                    ->scalarNode('backend')
                        ->isRequired()
                    ->end()
                    ->arrayNode('root_items')
                        ->isRequired()
                        ->performNoDeepMerging()
                        ->prototype('integer')->end()
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
                    ->integerNode('default_limit')
                        ->treatNullLike(0)
                        ->defaultValue(25)
                        ->min(0)
                    ->end()
                    ->scalarNode('template')
                        ->defaultValue('NetgenContentBrowserBundle:ezpublish:item.html.twig')
                    ->end()
                    ->arrayNode('types')
                        ->performNoDeepMerging()
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('category_types')
                        ->performNoDeepMerging()
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('columns')
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('template')->end()
                                ->scalarNode('value_provider')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('default_columns')
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->defaultValue(array('name', 'type', 'visible'))
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
