<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Config;

use Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader;
use Netgen\Bundle\ContentBrowserBundle\Config\Configuration;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\ConfigProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class ConfigLoaderTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadDefaultConfig
     */
    public function testLoadConfig()
    {
        $configLoader = new ConfigLoader(
            array(
                new ConfigProcessor(false),
                new ConfigProcessor(true),
            )
        );

        $configuration = new Configuration('test');
        $configuration->setParameter('one', 'default');
        $configuration->setParameter('three', 'default');

        $container = new Container();
        $container->set(
            'netgen_content_browser.config.test',
            $configuration
        );

        $configLoader->setContainer($container);

        $config = $configLoader->loadConfig('test');

        self::assertTrue($config->hasParameter('one'));
        self::assertEquals('config', $config->getParameter('one'));

        self::assertTrue($config->hasParameter('two'));
        self::assertEquals('config', $config->getParameter('two'));

        self::assertTrue($config->hasParameter('three'));
        self::assertEquals('default', $config->getParameter('three'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadDefaultConfig
     */
    public function testLoadConfigWithNoConfigProcessors()
    {
        $configLoader = new ConfigLoader();

        $configuration = new Configuration('test');
        $configuration->setParameter('one', 'default');
        $configuration->setParameter('three', 'default');

        $container = new Container();
        $container->set(
            'netgen_content_browser.config.test',
            $configuration
        );

        $configLoader->setContainer($container);

        $config = $configLoader->loadConfig('test');

        self::assertTrue($config->hasParameter('one'));
        self::assertEquals('default', $config->getParameter('one'));

        self::assertTrue($config->hasParameter('three'));
        self::assertEquals('default', $config->getParameter('three'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadDefaultConfig
     */
    public function testLoadConfigWithNoSupportedConfigProcessors()
    {
        $configLoader = new ConfigLoader(
            array(
                new ConfigProcessor(false),
            )
        );

        $configuration = new Configuration('test');
        $configuration->setParameter('one', 'default');
        $configuration->setParameter('three', 'default');

        $container = new Container();
        $container->set(
            'netgen_content_browser.config.test',
            $configuration
        );

        $configLoader->setContainer($container);

        $config = $configLoader->loadConfig('test');

        self::assertTrue($config->hasParameter('one'));
        self::assertEquals('default', $config->getParameter('one'));

        self::assertTrue($config->hasParameter('three'));
        self::assertEquals('default', $config->getParameter('three'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadConfig
     * @covers \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoader::loadDefaultConfig
     * @expectedException \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException
     */
    public function testLoadConfigThrowsInvalidArgumentException()
    {
        $configLoader = new ConfigLoader();

        $container = new Container();
        $container->set(
            'netgen_content_browser.config.test',
            new Configuration('test')
        );

        $configLoader->setContainer($container);

        $configLoader->loadConfig('non_existing');
    }
}
