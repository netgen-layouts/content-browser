<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ContentBrowserMultipleType extends AbstractContentBrowserMultipleType
{
    protected function getEntryType(): string
    {
        return TextType::class;
    }
}
