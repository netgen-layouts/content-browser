<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $clientContainer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $backendMock;

    public function setUp()
    {
        parent::setUp();

        $this->setUpClient();
        $this->mockBackend();
        $this->mockItemRenderer();

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    public function tearDown()
    {
        // We need an empty method to disable tearing down since it is
        // not compatible with Symfony 4.1
    }

    public function setUpClient()
    {
        parent::setUpClient();

        // We're using the container from kernel to bypass injection of
        // Symfony\Bundle\FrameworkBundle\Test\TestContainer on Symfony 4.1
        $this->clientContainer = static::$kernel->getContainer();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', getenv('SF_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', getenv('SF_PASSWORD'));
    }

    /**
     * Asserts that response is empty and has No Content status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function assertEmptyResponse(Response $response)
    {
        $this->assertEmpty($response->getContent());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * Asserts that response has a proper JSON exception content.
     * If statusCode is set, asserts that response has given status code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     * @param string $message
     */
    protected function assertException(Response $response, $statusCode = Response::HTTP_BAD_REQUEST, $message = null)
    {
        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, $statusCode);
        $this->assertHeader($response, 'application/json');
        $this->assertExceptionResponse($response, $statusCode, $message);
    }

    /**
     * Asserts that exception response has a correct response status text and code.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int $statusCode
     * @param string $message
     */
    protected function assertExceptionResponse(Response $response, $statusCode = Response::HTTP_BAD_REQUEST, $message = null)
    {
        $responseContent = json_decode($response->getContent(), true);
        $this->assertInternalType('array', $responseContent);

        $this->assertArrayHasKey('status_code', $responseContent);
        $this->assertArrayHasKey('status_text', $responseContent);

        $this->assertEquals($statusCode, $responseContent['status_code']);
        $this->assertEquals(Response::$statusTexts[$statusCode], $responseContent['status_text']);

        if ($message !== null) {
            $this->assertEquals($message, $responseContent['message']);
        }
    }

    protected function mockBackend()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = $this->clientContainer->get('netgen_content_browser.registry.backend');
        $backendRegistry->addBackend('test', $this->backendMock);

        $this->clientContainer->set(
            'netgen_content_browser.config.test',
            new Configuration(
                'test',
                array(
                    'columns' => array(
                        'name' => array(
                            'name' => 'columns.name',
                            'value_provider' => 'name',
                        ),
                    ),
                    'default_columns' => array('name'),
                    'preview' => array(
                        'enabled' => true,
                    ),
                )
            )
        );
    }

    protected function mockItemRenderer()
    {
        /** @var \Mockery\MockInterface $itemRendererMock */
        $itemRendererMock = $this->clientContainer->mock(
            'netgen_content_browser.item_renderer',
            ItemRendererInterface::class
        );

        $itemRendererMock
            ->shouldReceive('renderItem')
            ->andReturn('rendered item');
    }
}
