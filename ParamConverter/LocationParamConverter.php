<?php

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Netgen\Bundle\ContentBrowserBundle\Exceptions\InvalidArgumentException;

class LocationParamConverter implements ParamConverterInterface
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
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->attributes->has('locationId') || !$request->attributes->has('itemType')) {
            return false;
        }

        $locationId = $request->attributes->get('locationId');
        // 0 is a valid location ID
        if ($locationId === null || $locationId === '') {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new InvalidArgumentException('Required request attribute "locationId" is empty');
        }

        $request->attributes->set(
            'location',
            $this->itemRepository->loadLocation(
                $locationId,
                $request->attributes->get('itemType')
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
    public function supports(ParamConverter $configuration)
    {
        return is_a($configuration->getClass(), LocationInterface::class, true);
    }
}
