<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $valueType;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param string $valueType
     * @param array $config
     */
    public function __construct($valueType, array $config = array())
    {
        $this->valueType = $valueType;
        $this->config = $config;
    }

    /**
     * Returns the value type.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
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
     * Returns the template used to render the item.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->config['template'];
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
