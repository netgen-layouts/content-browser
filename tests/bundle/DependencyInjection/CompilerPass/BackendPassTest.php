<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class BackendPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_content_browser.registry.backend', new Definition(null, [[]]));

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend', ['item_type' => 'test']);
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_content_browser.registry.backend',
            0,
            ['test' => new Reference('netgen_content_browser.backend.test')]
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendPass::process
     * @expectedException \Netgen\ContentBrowser\Exceptions\RuntimeException
     * @expectedExceptionMessage Backend definition must have a 'item_type' attribute in its' tag.
     */
    public function testProcessThrowsRuntimeExceptionWithNoTagType(): void
    {
        $this->setDefinition('netgen_content_browser.registry.backend', new Definition());

        $backend = new Definition();
        $backend->addTag('netgen_content_browser.backend');
        $this->setDefinition('netgen_content_browser.backend.test', $backend);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass\BackendPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BackendPass());
    }
}
