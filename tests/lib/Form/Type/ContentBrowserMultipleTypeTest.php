<?php

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserMultipleTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry();
        $backendRegistry->addBackend('value', $this->backendMock);

        return new ContentBrowserMultipleType($backendRegistry, ['value' => 'Value']);
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit([42, 24]);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals([42, 24], $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItemNames
     */
    public function testBuildView()
    {
        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42)));

        $this->backendMock
            ->expects($this->at(1))
            ->method('loadItem')
            ->with($this->equalTo(24))
            ->will($this->returnValue(new Item(24)));

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            [
                'item_type' => 'value',
                'min' => 3,
                'max' => 5,
            ]
        );

        $form->submit([42, 24]);

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('item_names', $view->vars);
        $this->assertArrayHasKey('min', $view->vars);
        $this->assertArrayHasKey('max', $view->vars);

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals(
            [
                42 => 'This is a name (42)',
                24 => 'This is a name (24)',
            ],
            $view->vars['item_names']
        );

        $this->assertEquals(3, $view->vars['min']);
        $this->assertEquals(5, $view->vars['max']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItemNames
     */
    public function testBuildViewWithNonExistingItem()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit([42]);

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('item_names', $view->vars);

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals([], $view->vars['item_names']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItemNames
     */
    public function testBuildViewWithEmptyData()
    {
        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit(null);

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('item_names', $view->vars);

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals([], $view->vars['item_names']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
                'min' => 3,
                'max' => 5,
            ]
        );

        $this->assertEquals($options['item_type'], 'value');
        $this->assertEquals($options['min'], 3);
        $this->assertEquals($options['max'], 5);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithNormalizedMax()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
                'min' => 3,
                'max' => 2,
            ]
        );

        $this->assertEquals($options['item_type'], 'value');
        $this->assertEquals($options['min'], 3);
        $this->assertEquals($options['max'], 3);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "item_type" is missing.
     */
    public function testConfigureOptionsWithMissingItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve([]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "item_type" with value 42 is expected to be of type "string", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 42]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "item_type" with value "non_existing" is invalid.
     */
    public function testConfigureOptionsWithNonExistingItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'non_existing']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "min" with value "min" is expected to be of type "int" or "null", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidMin()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'min' => 'min']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "max" with value "max" is expected to be of type "int" or "null", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidMax()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'max' => 'max']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getParent
     */
    public function testGetParent()
    {
        $this->assertEquals(CollectionType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ng_content_browser_multiple', $this->formType->getBlockPrefix());
    }
}
