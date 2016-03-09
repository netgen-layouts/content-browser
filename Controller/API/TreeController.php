<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Tree\TreeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Netgen\Bundle\ContentBrowserBundle\Item\Item;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TreeController extends BaseController
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Tree\TreeInterface
     */
    protected $tree;

    /**
     * Returns tree config.
     *
     * @param string $tree
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfig($tree)
    {
        $translator = $this->get('translator');
        $this->initTree($tree);

        $rootItems = array();
        foreach ($this->tree->getRootItems() as $item) {
            $rootItems[] = $this->serializeItem(
                $item,
                $this->tree->hasSubCategories($item)
            );
        }

        $config = $this->tree->getConfig();
        $data = array(
            'name' => $translator->trans('netgen_content_browser.trees.' . $tree . '.name'),
            'root_items' => $rootItems,
            'min_selected' => $config['min_selected'],
            'max_selected' => $config['max_selected'],
            'default_columns' => $config['default_columns'],
            'available_columns' => array(
                'id' => $translator->trans('netgen_content_browser.columns.id'),
                'parent_id' => $translator->trans('netgen_content_browser.columns.parent_id'),
                'name' => $translator->trans('netgen_content_browser.columns.name'),
            ),
        );

        $adapterColumns = $this->tree->getAdapter()->getColumns();
        foreach ($adapterColumns as $adapterColumn => $adapterColumnName) {
            $data['available_columns'][$adapterColumn] = $translator->trans($adapterColumnName);
        }

        return new JsonResponse($data);
    }

    /**
     * Loads all children of the specified item.
     *
     * @param string $tree
     * @param int|string $itemId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getItemChildren($tree, $itemId)
    {
        $this->initTree($tree);

        $item = $this->tree->getItem($itemId);
        $children = $this->tree->getChildren($item);

        $childrenData = array();
        foreach ($children as $child) {
            $childrenData[] = $this->serializeItem(
                $child,
                $this->tree->hasChildren($child)
            );
        }

        $data = array(
            'path' => $this->getItemPath($item),
            'children' => $childrenData,
        );

        return new JsonResponse($data);
    }

    /**
     * Loads all children of the specified item.
     *
     * @param string $tree
     * @param int|string $itemId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getItemCategories($tree, $itemId)
    {
        $this->initTree($tree);

        $item = $this->tree->getItem($itemId);
        $children = $this->tree->getSubCategories($item);

        $childrenData = array();
        foreach ($children as $child) {
            $childrenData[] = $this->serializeItem(
                $child,
                $this->tree->hasSubCategories($child)
            );
        }

        $data = array(
            'path' => $this->getItemPath($item),
            'children' => $childrenData,
        );

        return new JsonResponse($data);
    }

    /**
     * Searches for children with search text
     *
     * @param Request $request
     * @param string $tree
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search(Request $request, $tree)
    {
        $this->initTree($tree);

        $searchText = $request->query->get('searchText', '');
        if (empty($searchText)) {
            throw new InvalidArgumentException('Search text cannot be empty');
        }

        $children = $this->tree->search($searchText);

        $childrenData = array();
        foreach ($children as $child) {
            $childrenData[] = $this->serializeItem(
                $child,
                $this->tree->hasChildren($child)
            );
        }

        $data = array(
            'children' => $childrenData,
        );

        return new JsonResponse($data);
    }

    /**
     * Generates the item path.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Item $item
     *
     * @return array
     */
    protected function getItemPath(Item $item)
    {
        $path = array();
        foreach ($item->path as $pathItemId) {
            $pathItem = $this->tree->getItem($pathItemId);
            if (!$this->tree->isInsideRootItems($pathItem)) {
                continue;
            }

            $path[] = array(
                'id' => $pathItem->id,
                'name' => $pathItem->name,
            );
        }

        return $path;
    }

    /**
     * Serializes the item.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\Item $item
     * @param bool $hasChildren
     *
     * @return array
     */
    protected function serializeItem(Item $item, $hasChildren = false)
    {
        $columns = array(
            'id' => $item->id,
            'parent_id' => !$this->tree->isRootItem($item) ?
                $item->parentId :
                null,
            'name' => $item->name,
            'selectable' => $item->isSelectable,
        ) + $item->additionalColumns;

        return $columns + array(
            'has_children' => (bool)$hasChildren,
            'html' => $this->renderView(
                $this->tree->getConfig()['template'],
                array(
                    'item' => $item,
                )
            ),
        );
    }

    /**
     * Builds the tree.
     *
     * @param string $tree
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If tree does not exist
     */
    protected function initTree($tree)
    {
        if ($this->tree instanceof TreeInterface) {
            return;
        }

        if (!$this->has('netgen_content_browser.tree.' . $tree)) {
            throw new NotFoundException("Tree '{$tree}' does not exist.");
        }

        $this->tree = $this->get('netgen_content_browser.tree.' . $tree);
    }
}
