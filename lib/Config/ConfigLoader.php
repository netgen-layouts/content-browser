<?php

namespace Netgen\ContentBrowser\Config;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ConfigLoader implements ConfigLoaderInterface
{
    use ContainerAwareTrait;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigProcessorInterface[]
     */
    protected $configProcessors = array();

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Config\ConfigProcessorInterface[] $configProcessors
     */
    public function __construct(array $configProcessors = array())
    {
        $this->configProcessors = $configProcessors;
    }

    /**
     * Loads the configuration for provided item type.
     *
     * @param string $itemType
     * @param string $configName
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config could not be found
     *
     * @return \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    public function loadConfig($itemType, $configName)
    {
        $config = $this->loadDefaultConfig($itemType);

        foreach ($this->configProcessors as $configProcessor) {
            if ($configProcessor->getItemType() !== $itemType) {
                continue;
            }

            if (!$configProcessor->supports($configName)) {
                continue;
            }

            $configProcessor->processConfig($itemType, $config);

            break;
        }

        return $config;
    }

    /**
     * Loads the default configuration for provided item type.
     *
     * @param string $itemType
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config could not be found
     *
     * @return \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    protected function loadDefaultConfig($itemType)
    {
        $service = 'netgen_content_browser.config.' . $itemType;

        if (!$this->container->has($service)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" item type does not exist.',
                    $itemType
                )
            );
        }

        return $this->container->get($service);
    }
}
