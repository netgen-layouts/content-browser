<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class ItemParamConverter implements ParamConverterInterface
{
    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    private $backendRegistry;

    public function __construct(BackendRegistryInterface $backendRegistry)
    {
        $this->backendRegistry = $backendRegistry;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$request->attributes->has('itemId') || !$request->attributes->has('itemType')) {
            return false;
        }

        $itemId = $request->attributes->get('itemId');
        if (empty($itemId)) {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new InvalidArgumentException('Required request attribute "itemId" is empty');
        }

        $backend = $this->backendRegistry->getBackend($request->attributes->get('itemType'));
        $request->attributes->set('item', $backend->loadItem($itemId));

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), ItemInterface::class, true);
    }
}
