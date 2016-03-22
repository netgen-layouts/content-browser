<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Config;

use Netgen\Bundle\ContentBrowserBundle\Config\DefaultConfigLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\DefaultConfigLoader
     */
    protected $defaultConfigLoader;

    public function setUp()
    {
        $this->containerMock = self::getMock(ContainerInterface::class);

        $this->defaultConfigLoader = new DefaultConfigLoader();
        $this->defaultConfigLoader->setContainer($this->containerMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\DefaultConfigLoader::loadConfig
     */
    public function testLoadConfig()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with('netgen_content_browser.config.test')
            ->will($this->returnValue(true));

        $this->containerMock
            ->expects($this->once())
            ->method('getParameter')
            ->with('netgen_content_browser.config.test')
            ->will($this->returnValue(array('config')));

        $config = $this->defaultConfigLoader->loadConfig('test');
        self::assertEquals(array('config'), $config);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\DefaultConfigLoader::loadConfig
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testLoadConfigWithNoConfig()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with('netgen_content_browser.config.test')
            ->will($this->returnValue(false));

        $this->defaultConfigLoader->loadConfig('test');
    }
}
