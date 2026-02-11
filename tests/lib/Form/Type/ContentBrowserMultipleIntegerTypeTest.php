<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleIntegerType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(ContentBrowserMultipleIntegerType::class)]
final class ContentBrowserMultipleIntegerTypeTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(
            ContentBrowserMultipleIntegerType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit([42, 24]);

        self::assertTrue($form->isSynchronized());
        self::assertSame([42, 24], $form->getData());
    }

    public function testBuildView(): void
    {
        $item1 = new Item(42);
        $item2 = new Item(24);

        $this->backendStub
            ->method('loadItem')
            ->willReturnMap(
                [
                    [42, $item1],
                    [24, $item2],
                ],
            );

        $form = $this->factory->create(
            ContentBrowserMultipleIntegerType::class,
            null,
            [
                'item_type' => 'value',
                'min' => 3,
                'max' => 5,
            ],
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
            $view->vars['items'],
        );

        self::assertSame(3, $view->vars['min']);
        self::assertSame(5, $view->vars['max']);
    }

    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendStub
            ->method('loadItem')
            ->willThrowException(new NotFoundException());

        $form = $this->factory->create(
            ContentBrowserMultipleIntegerType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit([42]);

        $view = $form->createView();

        self::assertArrayHasKey('items', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertSame([], $view->vars['items']);
        self::assertSame('value', $view->vars['item_type']);
    }

    public function testBuildViewWithEmptyData(): void
    {
        $form = $this->factory->create(
            ContentBrowserMultipleIntegerType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit(null);

        $view = $form->createView();

        self::assertArrayHasKey('items', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertSame([], $view->vars['items']);
        self::assertSame('value', $view->vars['item_type']);
    }

    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
                'min' => 3,
                'max' => 5,
            ],
        );

        self::assertSame('value', $options['item_type']);
        self::assertSame(3, $options['min']);
        self::assertSame(5, $options['max']);
    }

    public function testConfigureOptionsWithNormalizedMax(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
                'min' => 3,
                'max' => 2,
            ],
        );

        self::assertSame('value', $options['item_type']);
        self::assertSame(3, $options['min']);
        self::assertSame(3, $options['max']);
    }

    public function testConfigureOptionsWithMissingItemType(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "item_type" is missing.');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    public function testConfigureOptionsWithInvalidItemType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "item_type" with value 42 is expected to be of type "string", but is of type "int".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 42]);
    }

    public function testConfigureOptionsWithNonExistingItemType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "item_type" with value "non_existing" is invalid.');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'non_existing']);
    }

    public function testConfigureOptionsWithInvalidMin(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "min" with value "min" is expected to be of type "int" or "null", but is of type "string".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'min' => 'min']);
    }

    public function testConfigureOptionsWithInvalidMax(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "max" with value "max" is expected to be of type "int" or "null", but is of type "string".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['item_type' => 'value', 'max' => 'max']);
    }

    public function testGetParent(): void
    {
        self::assertSame(CollectionType::class, $this->formType->getParent());
    }

    public function testGetBlockPrefix(): void
    {
        self::assertSame('ngcb_multiple', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->backendStub = self::createStub(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendStub]);

        return new ContentBrowserMultipleIntegerType($backendRegistry);
    }
}
