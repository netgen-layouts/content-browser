<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ContentBrowserType extends AbstractContentBrowserType
{
    public function getParent(): string
    {
        return TextType::class;
    }
}
