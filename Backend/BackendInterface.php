<?php

namespace Netgen\Bundle\ContentBrowserBundle\Backend;

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
     * @param int|string $valueId
     * @param array $params
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface[]
     */
    public function getChildren($valueId, array $params = array());

    /**
     * Returns the value children count.
     *
     * @param int|string $valueId
     * @param array $params
     *
     * @return int
     */
    public function getChildrenCount($valueId, array $params = array());

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
