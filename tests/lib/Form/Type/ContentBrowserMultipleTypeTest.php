<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserMultipleTypeTest extends TestCase
{
    private MockObject $backendMock;

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItems
     */
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

        self::assertTrue($form->isSynchronized());
        self::assertSame(['42', '24'], $form->getData());
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
            ->method('loadItem')
            ->withConsecutive(
                [self::identicalTo('42')],
                [self::identicalTo('24')]
            )
            ->willReturnOnConsecutiveCalls($item1, $item2);

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

        self::assertArrayHasKey('items', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);
        self::assertArrayHasKey('min', $view->vars);
        self::assertArrayHasKey('max', $view->vars);

        self::assertSame('value', $view->vars['item_type']);
        self::assertSame(
            [
                42 => $item1,
                24 => $item2,
            ],
            $view->vars['items']
        );

        self::assertSame(3, $view->vars['min']);
        self::assertSame(5, $view->vars['max']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItems
     */
    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendMock
            ->expects(self::once())
            ->method('loadItem')
            ->with(self::identicalTo('42'))
            ->willThrowException(new NotFoundException());

        $form = $this->factory->create(
            ContentBrowserMultipleType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit([42]);

        $view = $form->createView();

        self::assertArrayHasKey('items', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertSame([], $view->vars['items']);
        self::assertSame('value', $view->vars['item_type']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::buildView
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getItems
     */
    public function testBuildViewWithEmptyData(): void
    {
        $this->backendMock
            ->expects(self::never())
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

        self::assertArrayHasKey('items', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertSame([], $view->vars['items']);
        self::assertSame('value', $view->vars['item_type']);
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

        self::assertSame($options['item_type'], 'value');
        self::assertSame($options['min'], 3);
        self::assertSame($options['max'], 5);
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

        self::assertSame($options['item_type'], 'value');
        self::assertSame($options['min'], 3);
        self::assertSame($options['max'], 3);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithMissingItemType(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "item_type" is missing.');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve([]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithInvalidItemType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessageMatches('/^The option "item_type" with value 42 is expected to be of type "string", but is of type "int(eger)?".$/');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 42]);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithNonExistingItemType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "item_type" with value "non_existing" is invalid.');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'non_existing']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithInvalidMin(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "min" with value "min" is expected to be of type "int" or "null", but is of type "string".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'min' => 'min']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::configureOptions
     */
    public function testConfigureOptionsWithInvalidMax(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "max" with value "max" is expected to be of type "int" or "null", but is of type "string".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'max' => 'max']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(CollectionType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        self::assertSame('ngcb_multiple', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        return new ContentBrowserMultipleType($backendRegistry);
    }
}
