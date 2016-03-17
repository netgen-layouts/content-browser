<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Converter\ConverterInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemReference;
use Twig_Environment;

class Builder implements BuilderInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    protected $config = array();

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Converter\ConverterInterface[]
     */
    protected $converters = array();

    public function __construct(BackendInterface $backend, Twig_Environment $twig, array $config)
    {
        $this->backend = $backend;
        $this->twig = $twig;
        $this->config = $config;
    }

    public function addConverter($itemType, ConverterInterface $converter)
    {
        $this->converters[$itemType] = $converter;
    }

    public function buildItem($itemType, $valueObject)
    {
        $childrenCount = $this->backend->getChildrenCount(
            array(
                'item_id' => $valueObject->id,
                'types' => $this->config['types'],
            )
        );

        $subCategoriesCount = $this->backend->getChildrenCount(
            array(
                'item_id' => $valueObject->id,
                'types' => $this->config['category_types'],
            )
        );

        $item = new Item();
        $item
            ->setId($this->converters[$itemType]->getId($valueObject))
            ->setValue($this->converters[$itemType]->getValue($valueObject))
            ->setTemplateVariables($this->converters[$itemType]->getTemplateVariables($valueObject))
            ->setParentId($this->converters[$itemType]->getParentId($valueObject))
            ->setName($this->converters[$itemType]->getName($valueObject))
            ->setIsSelectable($this->converters[$itemType]->getIsSelectable($valueObject))
            ->setHasChildren($childrenCount > 0)
            ->setHasSubCategories($subCategoriesCount > 0);

        $columns = array();
        $valueObjectColumns = $this->converters[$itemType]->getColumns($valueObject);

        foreach ($this->config['columns'] as $columnIdentifier => $columnConfig) {
            if (isset($columnConfig['template'])) {
                $columns[$columnIdentifier] = $this->twig->render(
                    $columnConfig['template'],
                    $item->getTemplateVariables()
                );
            } else {
                $columns[$columnIdentifier] = $valueObjectColumns[$columnIdentifier];
            }
        }

        $item->setColumns($columns);

        return $item;
    }

    public function buildItemReference($itemType, $valueObject)
    {
        $itemReference = new ItemReference();
        $itemReference
            ->setId($this->converters[$itemType]->getId($valueObject))
            ->setName($this->converters[$itemType]->getName($valueObject))
            ->setParentId($this->converters[$itemType]->getParentId($valueObject));

        return $itemReference;
    }
}
