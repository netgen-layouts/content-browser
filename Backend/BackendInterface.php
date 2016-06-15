<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;

interface BackendInterface
{
    /**
     * Returns the value type this backend supports.
     *
     * @return string
     */
    public function getValueType();

    /**
     * Returns the value children.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     * @param array $params
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[]
     */
    public function getChildren(ValueInterface $value, array $params = array());

    /**
     * Returns the value children count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     * @param array $params
     *
     * @return int
     */
    public function getChildrenCount(ValueInterface $value, array $params = array());

    /**
     * Searches for values.
     *
     * @param string $searchText
     * @param array $params
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[]
     */
    public function search($searchText, array $params = array());

    /**
     * Returns the count of searched values.
     *
     * @param string $searchText
     * @param array $params
     *
     * @return int
     */
    public function searchCount($searchText, array $params = array());
}
