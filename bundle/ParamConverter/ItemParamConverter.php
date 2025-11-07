<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

use function is_a;
use function mb_trim;

final class ItemParamConverter implements ParamConverterInterface
{
    public function __construct(
        private BackendRegistry $backendRegistry,
    ) {}

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (!$request->attributes->has('itemValue') || !$request->attributes->has('itemType')) {
            return false;
        }

        $itemValue = mb_trim($request->attributes->get('itemValue', ''));
        if ($itemValue === '') {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new InvalidArgumentException('Required request attribute "itemValue" is empty');
        }

        $backend = $this->backendRegistry->getBackend($request->attributes->get('itemType'));
        $request->attributes->set('item', $backend->loadItem($itemValue));

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return is_a($configuration->getClass(), ItemInterface::class, true);
    }
}
