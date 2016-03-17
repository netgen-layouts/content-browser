<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item;

class Item implements ItemInterface
{
    protected $id;

    protected $value;

    protected $parentId;

    protected $name;

    protected $isSelectable;

    protected $hasChildren;

    protected $hasSubCategories;

    protected $templateVariables = array();

    protected $columns = array();

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function isSelectable()
    {
        return $this->isSelectable;
    }

    public function setIsSelectable($isSelectable)
    {
        $this->isSelectable = $isSelectable;

        return $this;
    }

    public function hasChildren()
    {
        return $this->hasChildren;
    }

    public function setHasChildren($hasChildren)
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    public function hasSubCategories()
    {
        return $this->hasSubCategories;
    }

    public function setHasSubCategories($hasSubCategories)
    {
        $this->hasSubCategories = $hasSubCategories;

        return $this;
    }

    public function getTemplateVariables()
    {
        return $this->templateVariables;
    }

    public function setTemplateVariables(array $templateVariables)
    {
        $this->templateVariables = $templateVariables;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'parent_id' => $this->getParentId(),
            'name' => $this->getName(),
            'selectable' => $this->isSelectable(),
            'has_children' => $this->hasChildren(),
            'has_sub_categories' => $this->hasSubCategories(),
        ) + $this->getColumns();
    }
}
