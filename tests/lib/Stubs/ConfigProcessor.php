<?php

namespace Netgen\ContentBrowser\Tests\Stubs;

use Netgen\ContentBrowser\Config\ConfigProcessorInterface;

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
     * Returns the item type which this config supports.
     *
     * @return string
     */
    public function getItemType()
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
     * @param \Netgen\ContentBrowser\Config\ConfigurationInterface $config
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config could not be found
     */
    public function processConfig($configName, $config)
    {
        $config->setParameter('one', 'config');
        $config->setParameter('two', 'config');
    }
}
