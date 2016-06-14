<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemReference;
use Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

class ItemBuilder implements ItemBuilderInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\Builder\Converter\ConverterInterface[]
     */
    protected $converters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\BackendRegistryInterface $backendRegistry
     * @param array $config
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Builder\Converter\ConverterInterface[] $converters
     */
    public function __construct(
        BackendRegistryInterface $backendRegistry,
        array $config,
        array $converters = array()
    ) {
        $this->backendRegistry = $backendRegistry;
        $this->config = $config;
        $this->converters = $converters;
    }

    /**
     * Builds the item from provided value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function buildItem(ValueInterface $value)
    {
        $backend = $this->backendRegistry->getBackend($value->getValueType());
        $converter = $this->converters[$value->getValueType()];

        $subCategoriesCount = $backend->getChildrenCount(
            $value->getId(),
            array(
                'types' => $this->config['category_types'],
            )
        );

        return new Item(
            array(
                'id' => $value->getId(),
                'valueType' => $value->getValueType(),
                'value' => $value->getValue(),
                'parentId' => !in_array($value->getId(), $this->config['root_items']) ?
                    $value->getParentId() :
                    null,
                'name' => $value->getName(),
                'isSelectable' => $converter->getIsSelectable($value),
                'hasChildren' => $backend->getChildrenCount($value->getId()) > 0,
                'hasSubCategories' => $subCategoriesCount > 0,
                'object' => $value->getValueObject(),
            )
        );
    }

    /**
     * Builds the item reference from provided value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemReferenceInterface
     */
    public function buildItemReference(ValueInterface $value)
    {
        return new ItemReference(
            array(
                'id' => $value->getId(),
                'valueType' => $value->getValueType(),
                'value' => $value->getValue(),
                'name' => $value->getName(),
                'parentId' => !in_array($value->getId(), $this->config['root_items']) ?
                    $value->getParentId() :
                    null,
            )
        );
    }
}
