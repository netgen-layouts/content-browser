<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Utils\BackwardsCompatibility;

use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface as ContractsTranslatorInterface;

// Deprecated BC layer for Symfony 4.3 which deprecated TranslatorInterface from Translator component.
// Remove when support for Symfony 3.4 and lower ends.

if (interface_exists(ContractsTranslatorInterface::class)) {
    interface TranslatorInterface extends ContractsTranslatorInterface
    {
    }
} elseif (interface_exists(LegacyTranslatorInterface::class)) {
    interface TranslatorInterface extends LegacyTranslatorInterface
    {
    }
}
