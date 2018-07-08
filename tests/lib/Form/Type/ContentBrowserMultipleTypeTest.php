<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserMultipleTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    public function getMainType(): FormTypeInterface
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        return new ContentBrowserMultipleType($backendRegistry, ['value' => 'Value']);
    }

    public function testSubmitValidData(): void
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
        $this->assertSame(['42', '24'], $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItems
     */
    public function testBuildView(): void
    {
        $item1 = new Item(42);
        $item2 = new Item(24);

        $this->backendMock
            ->expects($this->at(0))
            ->method('loadItem')
            ->with($this->identicalTo('42'))
            ->will($this->returnValue($item1));

        $this->backendMock
            ->expects($this->at(1))
            ->method('loadItem')
            ->with($this->identicalTo('24'))
            ->will($this->returnValue($item2));

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

        $this->assertArrayHasKey('items', $view->vars);
        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('min', $view->vars);
        $this->assertArrayHasKey('max', $view->vars);

        $this->assertSame('value', $view->vars['item_type']);
        $this->assertSame(
            [
                42 => $item1,
                24 => $item2,
            ],
            $view->vars['items']
        );

        $this->assertSame(3, $view->vars['min']);
        $this->assertSame(5, $view->vars['max']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItems
     */
    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->identicalTo('42'))
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

        $this->assertArrayHasKey('items', $view->vars);
        $this->assertArrayHasKey('item_type', $view->vars);

        $this->assertSame([], $view->vars['items']);
        $this->assertSame('value', $view->vars['item_type']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItems
     */
    public function testBuildViewWithEmptyData(): void
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

        $this->assertArrayHasKey('items', $view->vars);
        $this->assertArrayHasKey('item_type', $view->vars);

        $this->assertSame([], $view->vars['items']);
        $this->assertSame('value', $view->vars['item_type']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptions(): void
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

        $this->assertSame($options['item_type'], 'value');
        $this->assertSame($options['min'], 3);
        $this->assertSame($options['max'], 5);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithNormalizedMax(): void
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

        $this->assertSame($options['item_type'], 'value');
        $this->assertSame($options['min'], 3);
        $this->assertSame($options['max'], 3);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "item_type" is missing.
     */
    public function testConfigureOptionsWithMissingItemType(): void
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
    public function testConfigureOptionsWithInvalidItemType(): void
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
    public function testConfigureOptionsWithNonExistingItemType(): void
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
    public function testConfigureOptionsWithInvalidMin(): void
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
    public function testConfigureOptionsWithInvalidMax(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'max' => 'max']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getParent
     */
    public function testGetParent(): void
    {
        $this->assertSame(CollectionType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertSame('ng_content_browser_multiple', $this->formType->getBlockPrefix());
    }
}
