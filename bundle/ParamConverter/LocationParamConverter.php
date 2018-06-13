<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class LocationParamConverter implements ParamConverterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    private $backendRegistry;

    public function __construct(BackendRegistryInterface $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
    }

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

    public function supports(ParamConverter $configuration)
    {
        return is_a($configuration->getClass(), LocationInterface::class, true);
    }
}
