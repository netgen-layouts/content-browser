<?php

namespace Netgen\ContentBrowser\Config;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $itemType;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param string $itemType
     * @param array $config
     * @param array $parameters
     */
    public function __construct($itemType, array $config = array(), array $parameters = array())
    {
        $this->itemType = $itemType;
        $this->config = $config;
        $this->parameters = $parameters;
    }

    /**
     * Returns the item type.
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Returns the minimum number of items allowed to be selected.
     *
     * @return int
     */
    public function getMinSelected()
    {
        return isset($this->config['min_selected']) ?
            $this->config['min_selected'] :
            1;
    }

    /**
     * Returns the maximum number of items allowed to be selected.
     *
     * @return int
     */
    public function getMaxSelected()
    {
        return isset($this->config['max_selected']) ?
            $this->config['max_selected'] :
            0;
    }

    /**
     * Returns if the tree is activated in the config.
     *
     * @return bool
     */
    public function hasTree()
    {
        return isset($this->config['tree']['enabled']) ?
            $this->config['tree']['enabled'] :
            false;
    }

    /**
     * Returns if the search is activated in the config.
     *
     * @return bool
     */
    public function hasSearch()
    {
        return isset($this->config['search']['enabled']) ?
            $this->config['search']['enabled'] :
            false;
    }

    /**
     * Returns if the preview is activated in the config.
     *
     * @return bool
     */
    public function hasPreview()
    {
        return isset($this->config['preview']['enabled']) ?
            $this->config['preview']['enabled'] :
            false;
    }

    /**
     * Returns the template used to render the item.
     *
     * @return string
     */
    public function getTemplate()
    {
        return isset($this->config['preview']['template']) ?
            $this->config['preview']['template'] :
            null;
    }

    /**
     * Returns the list of columns.
     *
     * @return array
     */
    public function getColumns()
    {
        return isset($this->config['columns']) ?
            $this->config['columns'] :
            array();
    }

    /**
     * Returns the list of default columns.
     *
     * @return array
     */
    public function getDefaultColumns()
    {
        return isset($this->config['default_columns']) ?
            $this->config['default_columns'] :
            array();
    }

    /**
     * Sets the parameter with specified name to specified value.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Adds the provided parameters to the config.
     *
     * Provided parameters will override any existing parameters.
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters)
    {
        $this->parameters = $parameters + $this->parameters;
    }

    /**
     * Returns if config has the specified parameter.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Returns the parameter with specified name.
     *
     * @param string $name
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException if parameter does not exist
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        if (!$this->hasParameter($name)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter "%s" does not exist in configuration.',
                    $name
                )
            );
        }

        return $this->parameters[$name];
    }

    /**
     * Returns all parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
