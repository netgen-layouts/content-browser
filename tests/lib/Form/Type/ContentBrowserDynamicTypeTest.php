<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserDynamicTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    public function getMainType(): FormTypeInterface
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value1' => $this->backendMock, 'value2' => $this->backendMock]);

        $configRegistry = new ConfigRegistry(
            [
                'value1' => new Configuration('value1', 'Value 1', []),
                'value2' => new Configuration('value2', 'Value 2', []),
            ]
        );

        return new ContentBrowserDynamicType($backendRegistry, $configRegistry);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildForm
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getEnabledItemTypes
     */
    public function testSubmitValidDataWithNoItemTypeLimit(): void
    {
        $form = $this->factory->create(
            ContentBrowserDynamicType::class
        );

        $data = ['item_type' => 'value2', 'item_value' => '42'];

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($data, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildForm
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getEnabledItemTypes
     */
    public function testSubmitValidDataWithItemTypeLimit(): void
    {
        $form = $this->factory->create(
            ContentBrowserDynamicType::class,
            null,
            [
                'item_types' => ['value1'],
            ]
        );

        $data = ['item_type' => 'value1', 'item_value' => '42'];

        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($data, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildView(): void
    {
        $item = new Item(42);

        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->identicalTo('42'))
            ->will($this->returnValue($item));

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $data = ['item_value' => 42, 'item_type' => 'value1'];

        $form->submit($data);

        $view = $form->createView();

        $this->assertArrayHasKey('item', $view->vars);
        $this->assertSame($item, $view->vars['item']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->identicalTo('42'))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $data = ['item_value' => 42, 'item_type' => 'value1'];

        $form->submit($data);

        $view = $form->createView();

        $this->assertArrayHasKey('item', $view->vars);
        $this->assertNull($view->vars['item']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::buildView
     */
    public function testBuildViewWithEmptyData(): void
    {
        $this->backendMock
            ->expects($this->never())
            ->method('loadItem');

        $form = $this->factory->create(ContentBrowserDynamicType::class);

        $form->submit(null);

        $view = $form->createView();

        $this->assertArrayHasKey('item', $view->vars);
        $this->assertNull($view->vars['item']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_types' => ['value1'],
            ]
        );

        $this->assertSame($options['item_types'], ['value1']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     */
    public function testConfigureOptionsWithMissingItemTypes(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $this->assertSame($options['item_types'], []);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "item_types" with value array is invalid.
     */
    public function testConfigureOptionsWithInvalidItemTypesItem(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_types' => [42]]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "item_types" with value 42 is expected to be of type "array", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidItemTypes(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_types' => 42]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertSame('ngcb_dynamic', $this->formType->getBlockPrefix());
    }
}
