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

    public function getItemType()
    {
        return $this->itemType;
    }

    public function getMinSelected()
    {
        return isset($this->config['min_selected']) ?
            $this->config['min_selected'] :
            1;
    }

    public function getMaxSelected()
    {
        return isset($this->config['max_selected']) ?
            $this->config['max_selected'] :
            0;
    }

    public function hasTree()
    {
        return isset($this->config['tree']['enabled']) ?
            $this->config['tree']['enabled'] :
            false;
    }

    public function hasSearch()
    {
        return isset($this->config['search']['enabled']) ?
            $this->config['search']['enabled'] :
            false;
    }

    public function hasPreview()
    {
        return isset($this->config['preview']['enabled']) ?
            $this->config['preview']['enabled'] :
            false;
    }

    public function getTemplate()
    {
        return isset($this->config['preview']['template']) ?
            $this->config['preview']['template'] :
            null;
    }

    public function getColumns()
    {
        return isset($this->config['columns']) ?
            $this->config['columns'] :
            array();
    }

    public function getDefaultColumns()
    {
        return isset($this->config['default_columns']) ?
            $this->config['default_columns'] :
            array();
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function addParameters(array $parameters)
    {
        $this->parameters = $parameters + $this->parameters;
    }

    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

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

    public function getParameters()
    {
        return $this->parameters;
    }
}
