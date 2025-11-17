<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ValueResolver;

use Netgen\Bundle\ContentBrowserBundle\ValueResolver\ItemValueResolver;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[CoversClass(ItemValueResolver::class)]
final class ItemValueResolverTest extends TestCase
{
    private MockObject $backendMock;

    private ItemValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        $this->valueResolver = new ItemValueResolver($backendRegistry);
    }

    public function testResolve(): void
    {
        $argument = new ArgumentMetadata('item', ItemInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('itemValue', '42');
        $request->attributes->set('itemType', 'value');

        $item = new Item(42);

        $this->backendMock
            ->expects(self::once())
            ->method('loadItem')
            ->with(self::identicalTo('42'))
            ->willReturn($item);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertSame($item, $values[0]);
    }

    public function testResolveWithMissingItemValue(): void
    {
        $argument = new ArgumentMetadata('item', ItemInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertSame([], $values);
    }

    public function testResolveWithMissingItemType(): void
    {
        $argument = new ArgumentMetadata('item', ItemInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('itemValue', '42');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertSame([], $values);
    }

    public function testResolveWithEmptyRequiredItemValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required request attribute "itemValue" is empty');

        $argument = new ArgumentMetadata('item', ItemInterface::class, false, false, null);

        $request = Request::create('/');
        $request->attributes->set('itemValue', '');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertSame([], $values);
    }
}
