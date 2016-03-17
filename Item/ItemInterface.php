<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

use JsonSerializable;

interface ItemInterface extends JsonSerializable
{
    public function getId();

    public function setId($id);

    public function getValue();

    public function setValue($value);

    public function getParentId();

    public function setParentId($parentId);

    public function getName();

    public function setName($name);

    public function isSelectable();

    public function setIsSelectable($isSelectable);

    public function hasChildren();

    public function setHasChildren($hasChildren);

    public function hasSubCategories();

    public function setHasSubCategories($hasSubCategories);

    public function getTemplateVariables();

    public function setTemplateVariables(array $templateVariables);

    public function getColumns();

    public function setColumns(array $columns);
}
