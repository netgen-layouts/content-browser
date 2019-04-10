<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    private $extension;

    public function __construct(ExtensionInterface $extension)
    {
        $this->extension = $extension;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->extension->getAlias());
        $rootNode = method_exists($treeBuilder, 'getRootNode') ?
            $treeBuilder->getRootNode() :
            $treeBuilder->root($this->extension->getAlias());

        $rootNode->children()
            ->arrayNode('item_types')
            ->useAttributeAsKey('identifier')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('min_selected')
                        ->treatNullLike(1)
                        ->defaultValue(1)
                        ->min(0)
                    ->end()
                    ->integerNode('max_selected')
                        ->treatNullLike(0)
                        ->defaultValue(0)
                        ->min(0)
                    ->end()
                    ->arrayNode('parameters')
                        ->defaultValue([])
                        ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('parameter')
                        ->prototype('variable')
                        ->end()
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
                            ->always(
                                static function (array $v): array {
                                    if ($v['enabled'] && !isset($v['template'])) {
                                        throw new InvalidConfigurationException('When preview is enabled, template needs to be specified');
                                    }

                                    return $v;
                                }
                            )
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
                            ->always(
                                static function (array $v): array {
                                    if (!isset($v['name'])) {
                                        throw new InvalidConfigurationException('Column with "name" identifier is required');
                                    }

                                    return $v;
                                }
                            )
                        ->end()
                        ->performNoDeepMerging()
                        ->prototype('array')
                            ->validate()
                                ->always(
                                    static function (array $v): array {
                                        $exception = new InvalidConfigurationException('Column specification needs to have either "template" or "value_provider" keys');

                                        if (isset($v['template'], $v['value_provider'])) {
                                            throw $exception;
                                        }

                                        if (!isset($v['template']) && !isset($v['value_provider'])) {
                                            throw $exception;
                                        }

                                        return $v;
                                    }
                                )
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
