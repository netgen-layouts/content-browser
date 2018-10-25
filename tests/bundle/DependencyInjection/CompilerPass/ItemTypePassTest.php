<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemTypePass;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ItemTypePassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemTypePass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_content_browser.registry.backend', new Definition(null, [[]]));
        $this->setDefinition('netgen_content_browser.registry.config', new Definition(null, [[]]));

        $this->setParameter(
            'netgen_content_browser.item_types',
            [
                'test' => [
                    'name' => 'item_types.test',
                    'preview' => [
                        'template' => 'template.html.twig',
                    ],
                    'parameters' => [],
                ],
            ]
        );

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend', ['item_type' => 'test']);
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.registry.backend',
            0,
            ['test' => new Reference('netgen_content_browser.backend.test')]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.registry.config',
            0,
            ['test' => new Reference('netgen_content_browser.config.test')]
        );

        self::assertFalse($this->container->hasParameter('netgen_content_browser.item_types'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemTypePass::process
     */
    public function testProcessThrowsRuntimeExceptionWithoutBackend(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No backend registered for "test" item type.');

        $this->setDefinition('netgen_content_browser.registry.backend', new Definition(null, [[]]));
        $this->setDefinition('netgen_content_browser.registry.config', new Definition(null, [[]]));

        $this->setParameter(
            'netgen_content_browser.item_types',
            [
                'test' => [
                    'name' => 'item_types.test',
                    'preview' => [
                        'template' => 'template.html.twig',
                    ],
                    'parameters' => [],
                ],
            ]
        );

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemTypePass::process
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Backend definition must have an "item_type" attribute in its tag.');

        $this->setDefinition('netgen_content_browser.registry.backend', new Definition());
        $this->setDefinition('netgen_content_browser.registry.config', new Definition(null, [[]]));

        $this->setParameter(
            'netgen_content_browser.item_types',
            [
                'test' => [
                    'name' => 'item_types.test',
                    'preview' => [
                        'template' => 'template.html.twig',
                    ],
                    'parameters' => [],
                ],
            ]
        );

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend');
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemTypePass::process
     */
    public function testProcessThrowsRuntimeExceptionWithInvalidItemType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Item type must begin with a letter and be followed by any combination of letters, digits and underscore, "Test type" given.');

        $this->setDefinition('netgen_content_browser.registry.backend', new Definition());
        $this->setDefinition('netgen_content_browser.registry.config', new Definition(null, [[]]));

        $this->setParameter(
            'netgen_content_browser.item_types',
            [
                'Test type' => [
                    'name' => 'item_types.test',
                    'preview' => [
                        'template' => 'template.html.twig',
                    ],
                    'parameters' => [],
                ],
            ]
        );

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend', ['item_type' => 'test']);
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\ItemTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ItemTypePass());
    }
}
