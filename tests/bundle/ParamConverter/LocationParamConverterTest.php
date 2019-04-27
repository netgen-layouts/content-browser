<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
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

    protected function setUp(): void
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

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
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn($location);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertSame($location, $request->attributes->get('location'));
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
            ->expects(self::never())
            ->method('loadLocation');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('location'));
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
            ->expects(self::never())
            ->method('loadLocation');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('location'));
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
            ->expects(self::never())
            ->method('loadLocation');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithEmptyRequiredLocationId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required request attribute "locationId" is empty');

        $configuration = new ParamConverter(
            [
                'class' => LocationInterface::class,
            ]
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('itemType', 'value');

        $this->backendMock
            ->expects(self::never())
            ->method('loadLocation');

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::supports
     */
    public function testSupports(): void
    {
        self::assertTrue($this->paramConverter->supports(new ParamConverter(['class' => LocationInterface::class])));
        self::assertFalse($this->paramConverter->supports(new ParamConverter(['class' => ItemInterface::class])));
    }
}
