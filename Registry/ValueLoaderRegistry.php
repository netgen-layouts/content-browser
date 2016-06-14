<?php

namespace Netgen\Bundle\ContentBrowserBundle\Registry;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;

class ValueLoaderRegistry implements ValueLoaderRegistryInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface[]
     */
    protected $valueLoaders = array();

    /**
     * Adds a value loader to registry.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader(ValueLoaderInterface $valueLoader)
    {
        $this->valueLoaders[$valueLoader->getValueType()] = $valueLoader;
    }

    /**
     * Returns if registry has a value loader.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasValueLoader($valueType)
    {
        return isset($this->valueLoaders[$valueType]);
    }

    /**
     * Returns a value loader for provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If value loader does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface
     */
    public function getValueLoader($valueType)
    {
        if (!$this->hasValueLoader($valueType)) {
            throw new InvalidArgumentException(
                sprintf('Value loader for "%s" value type does not exist.', $valueType)
            );
        }

        return $this->valueLoaders[$valueType];
    }

    /**
     * Returns all value loaders.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface[]
     */
    public function getValueLoaders()
    {
        return $this->valueLoaders;
    }
}
