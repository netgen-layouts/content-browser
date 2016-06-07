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
        $itemId = $this->converter->getId($valueObject);
        $parentId = $this->converter->getParentId($valueObject);

        $childrenCount = $this->backend->getChildrenCount($itemId);

        $subCategoriesCount = $this->backend->getChildrenCount(
            $itemId,
            array(
                'types' => $this->config['category_types'],
            )
        );

        $templateVariables = $this->converter->getTemplateVariables($valueObject);

        $itemData = array(
            'id' => $itemId,
            'value' => $this->converter->getValue($valueObject),
            'parentId' => !in_array($itemId, $this->config['root_items']) ? $parentId : null,
            'name' => $this->converter->getName($valueObject),
            'isSelectable' => $this->converter->getIsSelectable($valueObject),
            'hasChildren' => $childrenCount > 0,
            'hasSubCategories' => $subCategoriesCount > 0,
            'templateVariables' => $templateVariables,
        );

        $columns = array();
        $valueObjectColumns = $this->converter->getColumns($valueObject);

        foreach ($this->config['columns'] as $columnIdentifier => $columnConfig) {
            if (isset($columnConfig['template'])) {
                $columns[$columnIdentifier] = $this->twig->render(
                    $columnConfig['template'],
                    $templateVariables
                );
            } else {
                $columns[$columnIdentifier] = isset($valueObjectColumns[$columnIdentifier]) ?
                    $valueObjectColumns[$columnIdentifier] :
                    '';
            }
        }

        $itemData['columns'] = $columns;

        return new Item($itemData);
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

        return new ItemReference(
            array(
                'id' => $itemId,
                'name' => $this->converter->getName($valueObject),
                'parentId' => !in_array($itemId, $this->config['root_items']) ? $parentId : null,
            )
        );
    }
}
