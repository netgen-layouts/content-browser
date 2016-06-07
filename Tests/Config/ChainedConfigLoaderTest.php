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

    public function setUp()
    {
        $this->defaultConfigLoaderMock = self::getMock(DefaultConfigLoader::class);

        $this->defaultConfigLoaderMock
            ->expects($this->any())
            ->method('loadConfig')
            ->will($this->returnValue(array('one' => 'default', 'three' => 'default')));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ChainedConfigLoader::loadConfig
     */
    public function testLoadConfig()
    {
        $chainedConfigLoader = new ChainedConfigLoader(
            $this->defaultConfigLoaderMock,
            array(
                new UnsupportedConfigLoader(),
                new SupportedConfigLoader()
            )
        );

        $config = $chainedConfigLoader->loadConfig('test');
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
        $chainedConfigLoader = new ChainedConfigLoader(
            $this->defaultConfigLoaderMock,
            array()
        );

        $config = $chainedConfigLoader->loadConfig('test');
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
        $chainedConfigLoader = new ChainedConfigLoader(
            $this->defaultConfigLoaderMock,
            array(
                new UnsupportedConfigLoader(),
            )
        );

        $config = $chainedConfigLoader->loadConfig('test');
        self::assertEquals(
            array('one' => 'default', 'three' => 'default'),
            $config
        );
    }
}
