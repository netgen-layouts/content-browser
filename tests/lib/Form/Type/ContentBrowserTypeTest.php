<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\AbstractContentBrowserType;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(AbstractContentBrowserType::class)]
#[CoversClass(ContentBrowserType::class)]
final class ContentBrowserTypeTest extends TestCase
{
    private Stub&BackendInterface $backendStub;

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit('42');

        self::assertTrue($form->isSynchronized());
        self::assertSame('42', $form->getData());
    }

    public function testBuildView(): void
    {
        $item = new Item(42);

        $this->backendStub
            ->method('loadItem')
            ->willReturn($item);

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit('42');

        $view = $form->createView();

        self::assertArrayHasKey('item', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertSame($item, $view->vars['item']);
        self::assertSame('value', $view->vars['item_type']);
    }

    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendStub
            ->method('loadItem')
            ->willThrowException(new NotFoundException());

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit('42');

        $view = $form->createView();

        self::assertArrayHasKey('item', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertNull($view->vars['item']);
        self::assertSame('value', $view->vars['item_type']);
    }

    public function testBuildViewWithEmptyData(): void
    {
        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ],
        );

        $form->submit(null);

        $view = $form->createView();

        self::assertArrayHasKey('item', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertNull($view->vars['item']);
        self::assertSame('value', $view->vars['item_type']);
    }

    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
            ],
        );

        self::assertSame('value', $options['item_type']);
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

    public function testGetParent(): void
    {
        self::assertSame(TextType::class, $this->formType->getParent());
    }

    public function testGetBlockPrefix(): void
    {
        self::assertSame('ngcb', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->backendStub = self::createStub(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendStub]);

        return new ContentBrowserType($backendRegistry);
    }
}
