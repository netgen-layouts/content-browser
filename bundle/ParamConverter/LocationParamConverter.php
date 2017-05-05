<?php

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class LocationParamConverter implements ParamConverterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Registry\BackendRegistryInterface $backendRegistry
     */
    public function __construct(BackendRegistryInterface $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
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

        $backend = $this->backendRegistry->getBackend($request->attributes->get('itemType'));
        $request->attributes->set('location', $backend->loadLocation($locationId));

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
