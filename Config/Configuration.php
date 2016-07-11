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

    public function getValueType()
    {
        return $this->valueType;
    }

    public function getSections()
    {
        return $this->config['sections'];
    }

    public function setSections(array $sections)
    {
        $this->config['sections'] = $sections;
    }

    public function getMinSelected()
    {
        return $this->config['min_selected'];
    }

    public function setMinSelected($minSelected)
    {
        $this->config['min_selected'] = $minSelected;
    }

    public function getMaxSelected()
    {
        return $this->config['max_selected'];
    }

    public function setMaxSelected($maxSelected)
    {
        $this->config['max_selected'] = $maxSelected;
    }

    public function getTemplate()
    {
        return $this->config['template'];
    }

    public function getColumns()
    {
        return $this->config['columns'];
    }

    public function getDefaultColumns()
    {
        return $this->config['default_columns'];
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
