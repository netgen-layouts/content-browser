<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Form\Type;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserMultipleTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        return new ContentBrowserMultipleType($this->itemRepositoryMock);
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            array(
                'item_type' => 'value',
            )
        );

        $form->submit(array(42, 24));

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(array(42, 24), $form->getData());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::buildView
     */
    public function testBuildView()
    {
        $this->itemRepositoryMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->returnValue(new Item(42)));

        $this->itemRepositoryMock
            ->expects($this->at(1))
            ->method('loadItem')
            ->with($this->equalTo(24), $this->equalTo('value'))
            ->will($this->returnValue(new Item(24)));

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            array(
                'item_type' => 'value',
                'min' => 3,
                'max' => 5,
            )
        );

        $form->submit(array(42, 24));

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('config_name', $view->vars);
        $this->assertArrayHasKey('item_names', $view->vars);
        $this->assertArrayHasKey('min', $view->vars);
        $this->assertArrayHasKey('max', $view->vars);

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals('value', $view->vars['config_name']);
        $this->assertEquals(
            array(
                42 => 'This is a name (42)',
                24 => 'This is a name (24)',
            ),
            $view->vars['item_names']
        );

        $this->assertEquals(3, $view->vars['min']);
        $this->assertEquals(5, $view->vars['max']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::buildView
     */
    public function testBuildViewWithNonExistingItem()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42), $this->equalTo('value'))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            array(
                'item_type' => 'value',
            )
        );

        $form->submit(array(42));

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('config_name', $view->vars);
        $this->assertArrayHasKey('item_names', $view->vars);

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals('value', $view->vars['config_name']);
        $this->assertEquals(array(), $view->vars['item_names']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::buildView
     */
    public function testBuildViewWithEmptyData()
    {
        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadItem');

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            array(
                'item_type' => 'value',
            )
        );

        $form->submit(null);

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('config_name', $view->vars);
        $this->assertArrayHasKey('item_names', $view->vars);

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals('value', $view->vars['config_name']);
        $this->assertEquals(array(), $view->vars['item_names']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'item_type' => 'value',
                'min' => 3,
                'max' => 5,
            )
        );

        $this->assertEquals($options['item_type'], 'value');
        $this->assertEquals($options['config_name'], 'value');
        $this->assertEquals($options['min'], 3);
        $this->assertEquals($options['max'], 5);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithConfigName()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'item_type' => 'value',
                'config_name' => 'test',
            )
        );

        $this->assertEquals($options['item_type'], 'value');
        $this->assertEquals($options['config_name'], 'test');
        $this->assertEquals($options['min'], null);
        $this->assertEquals($options['max'], null);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithNormalizedMax()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'item_type' => 'value',
                'min' => 3,
                'max' => 2,
            )
        );

        $this->assertEquals($options['item_type'], 'value');
        $this->assertEquals($options['config_name'], 'value');
        $this->assertEquals($options['min'], 3);
        $this->assertEquals($options['max'], 3);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('config_name' => 'test'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_type' => 42));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidConfigName()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_type' => 'value', 'config_name' => 42));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidMin()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_type' => 'value', 'min' => 'min'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidMax()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_type' => 'value', 'max' => 'max'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::getParent
     */
    public function testGetParent()
    {
        $this->assertEquals(CollectionType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ng_content_browser_multiple', $this->formType->getBlockPrefix());
    }
}
