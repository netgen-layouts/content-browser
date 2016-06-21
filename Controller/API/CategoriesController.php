<?php

namespace Netgen\Bundle\ContentBrowserBundle\Controller\API;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoriesController extends Controller
{
    /**
     * Returns all subcategories of specified category.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface $category
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubCategories(CategoryInterface $category)
    {
        $data = array(
            'path' => $this->buildPath($category),
            'children' => $this->itemSerializer->serializeCategories(
                $this->itemRepository->getSubCategories($category)
            ),
        );

        return new JsonResponse($data);
    }
}
