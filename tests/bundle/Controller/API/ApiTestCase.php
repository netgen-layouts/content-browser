<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Stubs\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
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

    protected MockObject&BackendInterface $backendMock;

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
                    $this->mockItemRenderer(static::getContainer());
                },
            );
    }

    private function mockBackend(Container $container): void
    {
        $container->set(
            'netgen_content_browser.registry.backend',
            new BackendRegistry(['test' => $this->backendMock]),
        );

        $container->set(
            'netgen_content_browser.config.test',
            new Configuration(
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
                    ],
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
