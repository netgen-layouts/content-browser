<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserDynamicTypeTest extends TestCase
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
        $backendRegistry->addBackend('value1', $this->backendMock);
        $backendRegistry->addBackend('value2', $this->backendMock);

        return new ContentBrowserDynamicType(
            $backendRegistry,
            ['value1' => 'Value 1', 'value2' => 'Value 2']
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildForm
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getEnabledItemTypes
     */
    public function testSubmitValidDataWithNoItemTypeLimit()
    {
        $form = $this->factory->create(
            ContentBrowserDynamicType::class
        );

        $data = ['item_id' => '42', 'item_type' => 'value2'];

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildForm
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getEnabledItemTypes
     */
    public function testSubmitValidDataWithItemTypeLimit()
    {
        $form = $this->factory->create(
            ContentBrowserDynamicType::class,
            null,
            [
                'item_types' => ['value1'],
            ]
        );

        $data = ['item_id' => '42', 'item_type' => 'value1'];

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildView()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42)));

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $data = ['item_id' => 42, 'item_type' => 'value1'];

        $form->submit($data);

        $view = $form->createView();

        $this->assertArrayHasKey('item_name', $view->vars);
        $this->assertEquals('This is a name (42)', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildViewWithNonExistingItem()
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $data = ['item_id' => 42, 'item_type' => 'value1'];

        $form->submit($data);

        $view = $form->createView();

        $this->assertArrayHasKey('item_name', $view->vars);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildViewWithEmptyData()
    {
        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $form->submit(null);

        $view = $form->createView();

        $this->assertArrayHasKey('item_name', $view->vars);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_types' => ['value1'],
            ]
        );

        $this->assertEquals($options['item_types'], ['value1']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptionsWithMissingItemTypes()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $this->assertEquals($options['item_types'], []);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptionsWithInvalidItemTypesItem()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(['item_types' => [42]]);

        $this->assertEquals($options['item_types'], []);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "item_types" with value 42 is expected to be of type "array", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidItemTypes()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_types' => 42]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ng_content_browser_dynamic', $this->formType->getBlockPrefix());
    }
}
