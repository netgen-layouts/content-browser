<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class ContentBrowserMultipleIntegerType extends AbstractContentBrowserMultipleType
{
    protected function getEntryType(): string
    {
        return IntegerType::class;
    }
}
