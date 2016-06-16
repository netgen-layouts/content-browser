<?php

namespace Netgen\Bundle\ContentBrowserBundle\Item\Builder;

use Netgen\Bundle\ContentBrowserBundle\Item\Item;
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
            $value,
            array(
                'types' => $this->config['category_types'],
            )
        );

        return new Item(
            array(
                'id' => $value->getId(),
                'valueType' => $value->getValueType(),
                'value' => $value->getValue(),
                'parentId' => !in_array($value->getId(), $this->config['sections']) ?
                    $value->getParentId() :
                    null,
                'name' => $value->getName(),
                'isSelectable' => $converter->getIsSelectable($value),
                'hasChildren' => $backend->getChildrenCount($value) > 0,
                'hasSubCategories' => $subCategoriesCount > 0,
                'valueObject' => $value,
            )
        );
    }
}
