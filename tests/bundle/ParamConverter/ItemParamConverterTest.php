<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class ItemParamConverterTest extends TestCase
{
    private MockObject $backendMock;

    private ItemParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        $this->paramConverter = new ItemParamConverter($backendRegistry);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApply(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemValue', '42');
        $request->attributes->set('itemType', 'value');

        $item = new Item(42);

        $this->backendMock
            ->expects(self::once())
            ->method('loadItem')
            ->with(self::identicalTo('42'))
            ->willReturn($item);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertSame($item, $request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApplyWithMissingItemValue(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApplyWithMissingItemType(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemValue', '42');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApplyWithEmptyOptionalItemValue(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
                'isOptional' => true,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemValue', '');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApplyWithEmptyRequiredItemValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required request attribute "itemValue" is empty');

        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemValue', '');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->paramConverter->supports(new ParamConverter(['class' => ItemInterface::class])));
        self::assertFalse($this->paramConverter->supports(new ParamConverter(['class' => LocationInterface::class])));
    }
}
