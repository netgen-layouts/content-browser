<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

final class LocationParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry();
        $backendRegistry->addBackend('value', $this->backendMock);

        $this->paramConverter = new LocationParamConverter($backendRegistry);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApply(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => LocationInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('itemType', 'value');

        $location = new Location(42);

        $this->backendMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue($location));

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertSame($location, $request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithMissingLocationId(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => LocationInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithMissingItemType(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => LocationInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $this->backendMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithEmptyOptionalLocationId(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => LocationInterface::class,
                'isOptional' => true,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage Required request attribute "locationId" is empty
     */
    public function testApplyWithEmptyRequiredLocationId(): void
    {
        $configuration = new ParamConverter(
            [
                'class' => LocationInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::supports
     */
    public function testSupports(): void
    {
        $this->assertTrue($this->paramConverter->supports(new ParamConverter(['class' => LocationInterface::class])));
        $this->assertFalse($this->paramConverter->supports(new ParamConverter(['class' => ItemInterface::class])));
    }
}
