<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    /**
     * @var \Netgen\ContentBrowser\Tests\Kernel\MockerContainer
     */
    protected $clientContainer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $backendMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpClient();
        $this->mockBackend();
        $this->mockItemRenderer();

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    public function tearDown(): void
    {
        // We need an empty method to disable tearing down since it is
        // not compatible with Symfony 4.1
    }

    public function setUpClient(): void
    {
        parent::setUpClient();

        // We're using the container from kernel to bypass injection of
        // Symfony\Bundle\FrameworkBundle\Test\TestContainer on Symfony 4.1

        /** @var \Netgen\ContentBrowser\Tests\Kernel\MockerContainer $clientContainer */
        $clientContainer = static::$kernel->getContainer();

        $this->clientContainer = $clientContainer;

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', (string) getenv('SF_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', (string) getenv('SF_PASSWORD'));
    }

    /**
     * Asserts that response is empty and has No Content status code.
     */
    protected function assertEmptyResponse(Response $response): void
    {
        $this->assertEmpty($response->getContent());
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
        $responseContent = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $responseContent);

        $this->assertArrayHasKey('status_code', $responseContent);
        $this->assertArrayHasKey('status_text', $responseContent);

        $this->assertSame($statusCode, $responseContent['status_code']);
        $this->assertSame(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            $this->assertSame($message, $responseContent['message']);
        }
    }

    protected function mockBackend(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        /** @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface $backendRegistry */
        $backendRegistry = $this->clientContainer->get('netgen_content_browser.registry.backend');

        $backends = $backendRegistry->getBackends();
        $backends['test'] = $this->backendMock;

        $this->clientContainer->mock(
            'netgen_content_browser.registry.backend',
            new BackendRegistry($backends)
        );

        $this->clientContainer->set(
            'netgen_content_browser.config.test',
            new Configuration(
                'test',
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
                ]
            )
        );
    }

    protected function mockItemRenderer(): void
    {
        $itemRendererMock = $this->clientContainer->mock(
            'netgen_content_browser.item_renderer',
            $this->createMock(ItemRendererInterface::class)
        );

        $itemRendererMock
            ->expects($this->any())
            ->method('renderItem')
            ->will($this->returnValue('rendered item'));
    }
}
