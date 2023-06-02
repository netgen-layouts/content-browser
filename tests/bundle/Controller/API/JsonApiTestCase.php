<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\App\MockerContainer;
use Netgen\ContentBrowser\Tests\Stubs\BackendInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

use function getenv;
use function json_decode;

use const JSON_THROW_ON_ERROR;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    protected MockObject $backendMock;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpClient();

        $container = $this->client->getKernel()->getContainer();
        if (!$container instanceof MockerContainer) {
            throw new RuntimeException('Symfony kernel is not configured yet.');
        }

        $this->mockBackend($container);
        $this->mockItemRenderer($container);

        $this->expectedResponsesPath = __DIR__ . '/_responses/expected';
    }

    public function setUpClient(): void
    {
        parent::setUpClient();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', (string) getenv('SF_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', (string) getenv('SF_PASSWORD'));
    }

    /**
     * Asserts that response is empty and has No Content status code.
     */
    protected function assertEmptyResponse(Response $response): void
    {
        self::assertEmpty($response->getContent());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * Asserts that response has a proper JSON exception content.
     * If statusCode is set, asserts that response has given status code.
     */
    protected function assertException(Response $response, int $statusCode = Response::HTTP_BAD_REQUEST, ?string $message = null): void
    {
        if (($_SERVER['OPEN_ERROR_IN_BROWSER'] ?? false) === true) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, $statusCode);
        $this->assertHeader($response, 'application/json');
        $this->assertExceptionResponse($response, $statusCode, $message);
    }

    /**
     * Asserts that exception response has a correct response status text and code.
     */
    protected function assertExceptionResponse(Response $response, int $statusCode = Response::HTTP_BAD_REQUEST, ?string $message = null): void
    {
        $responseContent = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($responseContent);

        self::assertArrayHasKey('status_code', $responseContent);
        self::assertArrayHasKey('status_text', $responseContent);

        self::assertSame($statusCode, $responseContent['status_code']);
        self::assertSame(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            self::assertSame($message, $responseContent['message']);
        }
    }

    protected function mockBackend(MockerContainer $container): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        /** @var \Netgen\ContentBrowser\Registry\BackendRegistry $backendRegistry */
        $backendRegistry = $container->get('netgen_content_browser.registry.backend');

        $backends = $backendRegistry->getBackends();
        $backends['test'] = $this->backendMock;

        $container->mock(
            'netgen_content_browser.registry.backend',
            new BackendRegistry($backends),
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

    protected function mockItemRenderer(MockerContainer $container): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $itemRendererMock */
        $itemRendererMock = $container->mock(
            'netgen_content_browser.item_renderer',
            $this->createMock(ItemRendererInterface::class),
        );

        $itemRendererMock
            ->method('renderItem')
            ->willReturn('rendered item');
    }
}
