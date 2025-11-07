<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\ValueResolver;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

use function is_a;

final class LocationValueResolver implements ValueResolverInterface
{
    private BackendRegistry $backendRegistry;

    public function __construct(BackendRegistry $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
    }

    /**
     * @return iterable<\Netgen\ContentBrowser\Item\LocationInterface>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_a($argument->getType() ?? '', LocationInterface::class, true)) {
            return [];
        }

        if ($argument->getName() !== 'location') {
            return [];
        }

        if (!$request->attributes->has('locationId') || !$request->attributes->has('itemType')) {
            return [];
        }

        $locationId = $request->attributes->get('locationId');
        // 0 is a valid location ID
        if ($locationId === null || $locationId === '') {
            throw new InvalidArgumentException('Required request attribute "locationId" is empty');
        }

        $backend = $this->backendRegistry->getBackend($request->attributes->get('itemType'));

        yield $backend->loadLocation($locationId);
    }
}
