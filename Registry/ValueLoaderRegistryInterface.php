<?php

namespace Netgen\Bundle\ContentBrowserBundle\Registry;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface;

interface ValueLoaderRegistryInterface
{
    /**
     * Adds a value loader to registry.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface $valueLoader
     */
    public function addValueLoader(ValueLoaderInterface $valueLoader);

    /**
     * Returns if registry has a value loader.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasValueLoader($valueType);

    /**
     * Returns a value loader for provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If value loader does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface
     */
    public function getValueLoader($valueType);

    /**
     * Returns all value loaders.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueLoaderInterface[]
     */
    public function getValueLoaders();
}
