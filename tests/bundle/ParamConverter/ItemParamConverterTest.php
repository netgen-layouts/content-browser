<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class ItemParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry();
        $backendRegistry->addBackend('value', $this->backendMock);

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
        $request->attributes->set('itemId', 42);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42)));

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertEquals(new Item(42), $request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApplyWithMissingItemId(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('item'));
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
        $request->attributes->set('itemId', 42);

        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     */
    public function testApplyWithEmptyOptionalItemId(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
                'isOptional' => true,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemId', null);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('item'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::apply
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Required request attribute "itemId" is empty
     */
    public function testApplyWithEmptyRequiredItemId(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => ItemInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemId', null);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\ItemParamConverter::supports
     */
    public function testSupports(): void
    {
        $this->assertTrue($this->paramConverter->supports(new ParamConverter(['class' => ItemInterface::class])));
        $this->assertFalse($this->paramConverter->supports(new ParamConverter(['class' => LocationInterface::class])));
    }
}
