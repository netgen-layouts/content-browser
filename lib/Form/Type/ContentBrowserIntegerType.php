<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class ContentBrowserIntegerType extends AbstractContentBrowserType
{
    public function getParent(): string
    {
        return IntegerType::class;
    }
}
