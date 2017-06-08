<?php

namespace Netgen\ContentBrowser\Config;

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
     * Returns the sections.
     *
     * @return array
     */
    public function getSections()
    {
        return $this->config['sections'];
    }

    /**
     * Sets the sections.
     *
     * @param array $sections
     */
    public function setSections(array $sections)
    {
        $this->config['sections'] = $sections;
    }

    /**
     * Returns the minimum number of items allowed to be selected.
     *
     * @return int
     */
    public function getMinSelected()
    {
        return $this->config['min_selected'];
    }

    /**
     * Sets the minimum number of items allowed to be selected.
     *
     * @param int $minSelected
     */
    public function setMinSelected($minSelected)
    {
        $this->config['min_selected'] = $minSelected;
    }

    /**
     * Returns the maximum number of items allowed to be selected.
     *
     * @return int
     */
    public function getMaxSelected()
    {
        return $this->config['max_selected'];
    }

    /**
     * Sets the maximum number of items allowed to be selected.
     *
     * @param int $maxSelected
     */
    public function setMaxSelected($maxSelected)
    {
        $this->config['max_selected'] = $maxSelected;
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
        return $this->config['columns'];
    }

    /**
     * Returns the list of default columns.
     *
     * @return array
     */
    public function getDefaultColumns()
    {
        return $this->config['default_columns'];
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
     * @return mixed
     */
    public function getParameter($name)
    {
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
