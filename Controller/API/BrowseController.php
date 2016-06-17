<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;
use Netgen\Bundle\ContentBrowserBundle\Pagerfanta\ValueChildrenAdapter;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrowseController extends Controller
{
    /**
     * Returns all value objects with specified values.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException If values are missing or invalid.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getValues(Request $request)
    {
        $values = explode(',', $request->query->get('values'));
        if (!is_array($values) || empty($values)) {
            throw new InvalidArgumentException('List of values is invalid.');
        }

        $valueObjects = array();
        foreach ($values as $value) {
            $valueObjects[] = $this->valueLoader->loadByValue($value);
        }

        return new JsonResponse(
            array_map(
                function (ValueInterface $value) {
                    return $this->valueSerializer->serializeValue($value);
                },
                $valueObjects
            )
        );
    }

    /**
     * Returns all children of specified value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getChildren(ValueInterface $value, Request $request)
    {
        $pager = $this->buildPager(
            new ValueChildrenAdapter(
                $this->backend,
                $value
            ),
            $request
        );

        $data = array(
            'path' => $this->buildPath($value->getId()),
            'children_count' => $pager->getNbResults(),
            'children' => array_map(
                function (ValueInterface $value) {
                    return $this->valueSerializer->serializeValue($value);
                },
                $pager->getCurrentPageResults()
            ),
        );

        return new JsonResponse($data);
    }

    /**
     * Returns all subcategories of specified value.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface $value
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubCategories(ValueInterface $value)
    {
        $subCategories = $this->backend->getChildren(
            $value,
            array(
                'types' => $this->config['category_types'],
            )
        );

        $data = array(
            'path' => $this->buildPath($value->getId()),
            'children' => array_map(
                function (ValueInterface $value) {
                    return $this->valueSerializer->serializeValue($value);
                },
                $subCategories
            ),
        );

        return new JsonResponse($data);
    }
}
