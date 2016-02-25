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
            ->arrayNode('trees')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('identifier')
                ->prototype('array')
                    ->children()
                        ->arrayNode('root_locations')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
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
                        ->scalarNode('location_template')
                            ->validate()
                            ->ifTrue(function ($v) { return !is_string($v); })
                                ->thenInvalid('Location template should be a string')
                            ->end()
                            ->cannotBeEmpty()
                            ->defaultValue('NetgenContentBrowserBundle:ezpublish:location.html.twig')
                        ->end()
                        ->arrayNode('default_columns')
                            ->requiresAtLeastOneElement()
                            ->defaultValue(array('name', 'type', 'visible'))
                            ->prototype('scalar')
                                ->validate()
                                ->ifNotInArray(array('id', 'parent_id', 'name', 'thumbnail', 'type', 'visible'))
                                    ->thenInvalid('Invalid column name "%s"')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
