<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\ValueResolver;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

use function is_a;
use function mb_trim;

final class ItemValueResolver implements ValueResolverInterface
{
    public function __construct(
        private BackendRegistry $backendRegistry,
    ) {}

    /**
     * @return iterable<\Netgen\ContentBrowser\Item\ItemInterface>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_a($argument->getType() ?? '', ItemInterface::class, true)) {
            return [];
        }

        if ($argument->getName() !== 'item') {
            return [];
        }

        if (!$request->attributes->has('itemValue') || !$request->attributes->has('itemType')) {
            return [];
        }

        $itemValue = mb_trim($request->attributes->get('itemValue', ''));
        if ($itemValue === '') {
            throw new InvalidArgumentException('Required request attribute "itemValue" is empty');
        }

        $backend = $this->backendRegistry->getBackend($request->attributes->get('itemType'));

        yield $backend->loadItem($itemValue);
    }
}
