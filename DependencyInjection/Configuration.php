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
                            ->performNoDeepMerging()
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
                            ->performNoDeepMerging()
                            ->requiresAtLeastOneElement()
                            ->defaultValue(array('name', 'type', 'visible'))
                            ->prototype('scalar')
                                ->validate()
                                ->ifNotInArray(array('id', 'parent_id', 'name', 'thumbnail', 'type', 'visible', 'owner', 'modified', 'published', 'priority', 'section'))
                                    ->thenInvalid('Invalid column name "%s"')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('categories')
                            ->isRequired()
                            ->children()
                                ->arrayNode('types')
                                    ->isRequired()
                                    ->performNoDeepMerging()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')
                                        ->validate()
                                        ->ifTrue(function ($v) { return !is_string($v); })
                                            ->thenInvalid('Category type should be a string')
                                        ->end()
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('children')
                            ->children()
                                ->arrayNode('types')
                                    ->performNoDeepMerging()
                                    ->prototype('scalar')
                                        ->validate()
                                        ->ifTrue(function ($v) { return !is_string($v); })
                                            ->thenInvalid('Child type should be a string')
                                        ->end()
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                                ->booleanNode('include_category_types')
                                    ->defaultValue(true)
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
