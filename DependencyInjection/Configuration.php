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

        $children = $rootNode->children();
        $children->append($this->generateTreesConfig());

        $adapters = $children->arrayNode('adapters');
        $adapters->isRequired();

        $adapters->append($this->generateEzPublishAdapterConfig());

        $children->end();

        return $treeBuilder;
    }

    public function generateTreesConfig()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('trees');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('adapter')
                        ->isRequired()
                        ->validate()
                        ->ifTrue(function ($v) { return !is_string($v); })
                            ->thenInvalid('Adapter identifier should be a string')
                        ->end()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('root_items')
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
                    ->scalarNode('template')
                        ->validate()
                        ->ifTrue(function ($v) { return !is_string($v); })
                            ->thenInvalid('Item template should be a string')
                        ->end()
                        ->cannotBeEmpty()
                        ->defaultValue('NetgenContentBrowserBundle:ezpublish:item.html.twig')
                    ->end()
                    ->arrayNode('default_columns')
                        ->performNoDeepMerging()
                        ->requiresAtLeastOneElement()
                        ->defaultValue(array('name', 'type', 'visible'))
                        ->prototype('scalar')
                            ->validate()
                            ->ifTrue(function ($v) { return !is_string($v); })
                                ->thenInvalid('Column identifier should be a string')
                            ->end()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                    ->arrayNode('categories')
                        ->children()
                            ->arrayNode('types')
                                ->performNoDeepMerging()
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
            ->end();

        return $node;
    }

    public function generateEzPublishAdapterConfig()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('ezpublish');
        $node->isRequired();

        $node
            ->children()
                ->arrayNode('image_fields')
                    ->performNoDeepMerging()
                    ->requiresAtLeastOneElement()
                    ->defaultValue(array('image'))
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                        ->validate()
                        ->ifTrue(function ($v) { return !is_string($v); })
                            ->thenInvalid('Image field identifier should be a string')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('variation_name')
                    ->validate()
                    ->ifTrue(function ($v) { return !is_string($v); })
                        ->thenInvalid('Variation name should be a string')
                    ->end()
                    ->cannotBeEmpty()
                    ->defaultValue('netgen_content_browser')
                ->end()
            ->end();

        return $node;
    }
}
