<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Config\ConfigProcessorInterface;

class ConfigProcessor implements ConfigProcessorInterface
{
    /**
     * @var bool
     */
    protected $supports;

    /**
     * Constructor.
     *
     * @param bool $supports
     */
    public function __construct($supports = true)
    {
        $this->supports = $supports;
    }

    /**
     * Returns the value type which this config supports.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'test';
    }

    /**
     * Returns if the loader supports the config with provided name.
     *
     * @param string $configName
     *
     * @return bool
     */
    public function supports($configName)
    {
        return $this->supports;
    }

    /**
     * Processes the given config.
     *
     * @param string $configName
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigurationInterface $config
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If config could not be found
     */
    public function processConfig($configName, $config)
    {
        $config->setParameter('one', 'config');
        $config->setParameter('two', 'config');
    }
}
