<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Converter;

interface ConverterInterface
{
    public function getId($valueObject);

    public function getParentId($valueObject);

    public function getValue($valueObject);

    public function getName($valueObject);

    public function getIsSelectable($valueObject);

    public function getTemplateVariables($valueObject);

    public function getColumns($valueObject);
}
