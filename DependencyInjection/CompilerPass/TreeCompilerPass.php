<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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

        foreach ($trees as $tree) {
            $treeDefinition = new Definition(
                $container->getParameter('netgen_content_browser.tree.class'),
                array(
                    new Reference('netgen_content_browser.adapter'),
                    new Reference('translator'),
                    $container->getParameter('netgen_content_browser.tree.' . $tree)
                )
            );

            $container->setDefinition(
                'netgen_content_browser.tree.' . $tree,
                $treeDefinition
            );
        }
    }
}
