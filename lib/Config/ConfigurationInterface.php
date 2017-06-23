<?php

namespace Netgen\ContentBrowser\Config;

interface ConfigurationInterface
{
    /**
     * Returns the item type.
     *
     * @return string
     */
    public function getItemType();

    /**
     * Returns the minimum number of items allowed to be selected.
     *
     * @return int
     */
    public function getMinSelected();

    /**
     * Returns the maximum number of items allowed to be selected.
     *
     * @return int
     */
    public function getMaxSelected();

    /**
     * Returns if the tree is activated in the config.
     *
     * @return bool
     */
    public function hasTree();

    /**
     * Returns if the search is activated in the config.
     *
     * @return bool
     */
    public function hasSearch();

    /**
     * Returns if the preview is activated in the config.
     *
     * @return bool
     */
    public function hasPreview();

    /**
     * Returns the template used to render the item.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Returns the list of columns.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Returns the list of default columns.
     *
     * @return array
     */
    public function getDefaultColumns();

    /**
     * Sets the parameter with specified name to specified value.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setParameter($name, $value);

    /**
     * Adds the provided parameters to the config.
     *
     * Provided parameters will override any existing parameters.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters);

    /**
     * Returns if config has the specified parameter.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name);

    /**
     * Returns the parameter with specified name.
     *
     * @param string $name
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException if parameter does not exist
     *
     * @return mixed
     */
    public function getParameter($name);

    /**
     * Returns all parameters.
     *
     * @return array
     */
    public function getParameters();
}
