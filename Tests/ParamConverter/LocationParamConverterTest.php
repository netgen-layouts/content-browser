<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;
use Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Location;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class LocationParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        $this->paramConverter = new LocationParamConverter($this->itemRepositoryMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApply()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('valueType', 'value');

        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Location(42)));

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertEquals(new Location(42), $request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithMissingLocationId()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class
            )
        );

        $request = Request::create('/');
        $request->attributes->set('valueType', 'value');

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithMissingValueType()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', 42);

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     */
    public function testApplyWithEmptyOptionalLocationId()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
                'isOptional' => true,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('valueType', 'value');

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertNull($request->attributes->get('location'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::apply
     * @expectedException \UnexpectedValueException
     */
    public function testApplyWithEmptyRequiredLocationId()
    {
        $configuration = new ParamConverter(
            array(
                'class' => LocationInterface::class,
            )
        );

        $request = Request::create('/');
        $request->attributes->set('locationId', null);
        $request->attributes->set('valueType', 'value');

        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadLocation');

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\ParamConverter\LocationParamConverter::supports
     */
    public function testSupports()
    {
        self::assertTrue($this->paramConverter->supports(new ParamConverter(array('class' => LocationInterface::class))));
        self::assertFalse($this->paramConverter->supports(new ParamConverter(array('class' => ItemInterface::class))));
    }
}
