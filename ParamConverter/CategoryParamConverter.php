<?php

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\Item\CategoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class CategoryParamConverter implements ParamConverterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface $itemRepository
     */
    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Stores the object in the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request The request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverterConfiguration $configuration)
    {
        if (!$request->attributes->has('categoryId') || !$request->attributes->has('valueType')) {
            return false;
        };

        $categoryId = $request->attributes->get('categoryId');
        // 0 is a valid category ID
        if ($categoryId === null || $categoryId === '') {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new UnexpectedValueException('Required request attribute "categoryId" is empty');
        }

        $request->attributes->set(
            'category',
            $this->itemRepository->loadCategory(
                $categoryId,
                $request->attributes->get('valueType')
            )
        );

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverterConfiguration $configuration)
    {
        return is_a($configuration->getClass(), CategoryInterface::class, true);
    }
}
