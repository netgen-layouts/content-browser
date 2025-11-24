<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API;

use Zenstruck\Assert;
use Zenstruck\Browser;
use Zenstruck\Browser\KernelBrowser as BaseKernelBrowser;

use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class KernelBrowser extends BaseKernelBrowser
{
    public function assertJsonIs(string $expected): self
    {
        $decoded = json_decode(
            (string) file_get_contents(__DIR__ . '/_responses/expected/' . $expected . '.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $this->json()->is($decoded);

        return $this;
    }

    public function assertContentIs(string $expected): self
    {
        return $this->use(
            static function (Browser $browser) use ($expected): void {
                Assert::that($browser->content())->equals($expected);
            },
        );
    }
}
