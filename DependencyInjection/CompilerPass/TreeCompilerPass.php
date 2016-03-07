<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class TreeCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $trees = $container->getParameter('netgen_content_browser.trees');

        $adapters = $container->findTaggedServiceIds('netgen_content_browser.adapter');
        $adapters = array_map(
            function(array $v) {
                return $v[0]['identifier'];
            },
            $adapters
        );

        $adapters = array_flip($adapters);

        $treeClass = $container->getParameter('netgen_content_browser.tree.class');
        foreach ($trees as $tree) {
            $treeConfig = $container->getParameter('netgen_content_browser.tree.' . $tree);

            if (!isset($adapters[$treeConfig['adapter']])) {
                throw new RuntimeException("Adapter '{$treeConfig['adapter']}' does not exist.");
            }

            $treeDefinition = new Definition(
                $treeClass,
                array(
                    new Reference($adapters[$treeConfig['adapter']]),
                    new Reference('translator'),
                    $treeConfig
                )
            );

            $container->setDefinition(
                'netgen_content_browser.tree.' . $tree,
                $treeDefinition
            );
        }
    }
}
