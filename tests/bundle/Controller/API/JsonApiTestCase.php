<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Lakion\ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
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

        $this->expectedResponsesPath = __DIR__ . '/responses/expected';
    }

    public function setUpClient()
    {
        parent::setUpClient();

        $this->clientContainer = $this->client->getContainer();

        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
        $this->client->setServerParameter('PHP_AUTH_USER', getenv('EZ_USERNAME'));
        $this->client->setServerParameter('PHP_AUTH_PW', getenv('EZ_PASSWORD'));
    }

    /**
     * Asserts that response has JSON content.
     * If filename is set, asserts that response content matches the one in given file.
     * If statusCode is set, asserts that response has given status code.
     * If excludeElements is set, the items in it will not be checked for value equality but only for existence.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string $filename
     * @param int $statusCode
     * @param array $excludedElements
     */
    protected function assertResponse(Response $response, $filename, $statusCode = Response::HTTP_OK, $excludedElements = array())
    {
        $responseContent = json_decode($response->getContent(), true);
        if (is_array($responseContent)) {
            foreach ($excludedElements as $excludedElement) {
                $this->assertArrayHasKey($excludedElement, $responseContent);
                $this->assertNotEmpty($responseContent[$excludedElement]);
                unset($responseContent[$excludedElement]);
            }

            $response->setContent(json_encode($responseContent));
        }

        parent::assertResponse($response, $filename, $statusCode);
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

    /**
     * Pretty encodes the provided array.
     *
     * @param array $content
     *
     * @return string
     */
    protected function jsonEncode(array $content)
    {
        return json_encode($content, JSON_PRETTY_PRINT);
    }

    private function mockBackend()
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
                )
            )
        );
    }
}
