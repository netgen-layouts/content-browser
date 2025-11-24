<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Zenstruck\Browser\Test\HasBrowser;

abstract class ApiTestCase extends KernelTestCase
{
    use HasBrowser {
        browser as protected baseBrowser;
    }

    final protected MockObject&BackendInterface $backendMock;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $server
     */
    protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->baseBrowser($options, $server)
            ->actingAs(new InMemoryUser('admin', 'admin', ['ROLE_ADMIN']))
            ->use(
                function (): void {
                    $this->mockBackend(static::getContainer());
                    $this->mockConfig(static::getContainer());
                    $this->mockItemRenderer(static::getContainer());
                },
            );
    }

    private function mockBackend(Container $container): void
    {
        $container->set(
            'netgen_content_browser.registry.backend',
            new BackendRegistry(
                [
                    'test' => $this->backendMock,
                    'test_preview_disabled' => $this->backendMock,
                ],
            ),
        );
    }

    private function mockConfig(Container $container): void
    {
        $config = new Configuration(
            'test',
            'Test',
            [
                'columns' => [
                    'name' => [
                        'name' => 'columns.name',
                        'value_provider' => 'name',
                    ],
                ],
                'default_columns' => ['name'],
                'preview' => [
                    'enabled' => true,
                    'template' => 'template.html.twig',
                ],
            ],
        );

        $configPreviewDisabled = new Configuration(
            'test_preview_disabled',
            'Test (Preview disabled)',
            [
                'columns' => [
                    'name' => [
                        'name' => 'columns.name',
                        'value_provider' => 'name',
                    ],
                ],
                'default_columns' => ['name'],
                'preview' => [
                    'enabled' => false,
                ],
            ],
        );

        $container->set(
            'netgen_content_browser.registry.config',
            new ConfigRegistry(
                [
                    'test' => $config,
                    'test_preview_disabled' => $configPreviewDisabled,
                ],
            ),
        );
    }

    private function mockItemRenderer(Container $container): void
    {
        $itemRendererMock = $this->createMock(ItemRendererInterface::class);

        $itemRendererMock
            ->method('renderItem')
            ->willReturn('rendered item');

        $container->set('netgen_content_browser.item_renderer', $itemRendererMock);
    }
}
