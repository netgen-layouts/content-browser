<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Stubs;

use Netgen\Bundle\ContentBrowserBundle\Config\ValueTypeConfigLoaderInterface;

class ConfigLoader implements ValueTypeConfigLoaderInterface
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
     * Loads the configuration by its name.
     *
     * @param string $configName
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If config could not be found
     *
     * @return array
     */
    public function loadConfig($configName)
    {
        return array('one' => 'config', 'two' => 'config');
    }

    /**
     * Returns the value type which this config supports.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'ezcontent';
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
}
