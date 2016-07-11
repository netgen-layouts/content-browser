<?php

namespace Netgen\Bundle\ContentBrowserBundle\Config;

interface ConfigurationInterface
{
    public function getValueType();

    public function getSections();

    public function setSections(array $sections);

    public function getMinSelected();

    public function setMinSelected($minSelected);

    public function getMaxSelected();

    public function setMaxSelected($maxSelected);

    public function getTemplate();

    public function getColumns();

    public function getDefaultColumns();

    public function setParameter($name, $value);

    public function hasParameter($name);

    public function getParameter($name);

    public function getParameters();
}
