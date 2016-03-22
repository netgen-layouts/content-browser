<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Config;

use Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader;
use Netgen\Bundle\ContentBrowserBundle\Config\DefaultConfigLoader;
use Netgen\Bundle\ContentBrowserBundle\Tests\Config\Stubs\SupportedConfigLoader;
use Netgen\Bundle\ContentBrowserBundle\Tests\Config\Stubs\UnsupportedConfigLoader;

class ChainedConfigLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\DefaultConfigLoader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultConfigLoaderMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader
     */
    protected $chainedConfigLoader;

    public function setUp()
    {
        $this->defaultConfigLoaderMock = self::getMock(DefaultConfigLoader::class);

        $this->defaultConfigLoaderMock
            ->expects($this->any())
            ->method('loadConfig')
            ->will($this->returnValue(array('one' => 'default', 'three' => 'default')));

        $this->chainedConfigLoader = new ChainedConfigLoader($this->defaultConfigLoaderMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::addConfigLoader
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::loadConfig
     */
    public function testLoadConfig()
    {
        $this->chainedConfigLoader->addConfigLoader(new UnsupportedConfigLoader());
        $this->chainedConfigLoader->addConfigLoader(new SupportedConfigLoader());

        $config = $this->chainedConfigLoader->loadConfig('test');
        self::assertEquals(
            array('one' => 'supported', 'two' => 'supported', 'three' => 'default'),
            $config
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::loadConfig
     */
    public function testLoadConfigWithNoConfigLoaders()
    {
        $config = $this->chainedConfigLoader->loadConfig('test');
        self::assertEquals(
            array('one' => 'default', 'three' => 'default'),
            $config
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::loadConfig
     */
    public function testLoadConfigWithNoSupportedConfigLoaders()
    {
        $this->chainedConfigLoader->addConfigLoader(new UnsupportedConfigLoader());

        $config = $this->chainedConfigLoader->loadConfig('test');
        self::assertEquals(
            array('one' => 'default', 'three' => 'default'),
            $config
        );
    }
}
