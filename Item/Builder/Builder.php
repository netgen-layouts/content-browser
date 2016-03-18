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
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Converter\ConverterInterface
     */
    protected $converter;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface
     */
    protected $backend;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Converter\ConverterInterface $converter
     * @param \Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface $backend
     * @param \Twig_Environment $twig
     * @param array $config
     */
    public function __construct(
        ConverterInterface $converter,
        BackendInterface $backend,
        Twig_Environment $twig,
        array $config
    ) {
        $this->converter = $converter;
        $this->backend = $backend;
        $this->twig = $twig;
        $this->config = $config;
    }

    /**
     * Builds the item from provided value object.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function buildItem($valueObject)
    {
        $childrenCount = $this->backend->getChildrenCount($valueObject->id);

        $subCategoriesCount = $this->backend->getChildrenCount(
            $valueObject->id,
            array(
                'types' => $this->config['category_types'],
            )
        );

        $itemId = $this->converter->getId($valueObject);
        $parentId = $this->converter->getParentId($valueObject);

        $item = new Item();
        $item
            ->setId($itemId)
            ->setValue($this->converter->getValue($valueObject))
            ->setTemplateVariables($this->converter->getTemplateVariables($valueObject))
            ->setParentId(
                !in_array($itemId, $this->config['root_items']) ? $parentId : null
            )
            ->setName($this->converter->getName($valueObject))
            ->setIsSelectable($this->converter->getIsSelectable($valueObject))
            ->setHasChildren($childrenCount > 0)
            ->setHasSubCategories($subCategoriesCount > 0);

        $columns = array();
        $valueObjectColumns = $this->converter->getColumns($valueObject);

        foreach ($this->config['columns'] as $columnIdentifier => $columnConfig) {
            if (isset($columnConfig['template'])) {
                $columns[$columnIdentifier] = $this->twig->render(
                    $columnConfig['template'],
                    $item->getTemplateVariables()
                );
            } else {
                $columns[$columnIdentifier] = isset($valueObjectColumns[$columnIdentifier]) ?
                    $valueObjectColumns[$columnIdentifier] :
                    '';
            }
        }

        $item->setColumns($columns);

        return $item;
    }

    /**
     * Builds the item reference from provided value object.
     *
     * @param mixed $valueObject
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function buildItemReference($valueObject)
    {
        $itemId = $this->converter->getId($valueObject);
        $parentId = $this->converter->getParentId($valueObject);

        $itemReference = new ItemReference();
        $itemReference
            ->setId($itemId)
            ->setName($this->converter->getName($valueObject))
            ->setParentId(
                !in_array($itemId, $this->config['root_items']) ? $parentId : null
            );

        return $itemReference;
    }
}
