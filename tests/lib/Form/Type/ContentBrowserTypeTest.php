<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit('42');

        self::assertTrue($form->isSynchronized());
        self::assertSame('42', $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildView(): void
    {
        $item = new Item(42);

        $this->backendMock
            ->expects(self::once())
            ->method('loadItem')
            ->with(self::identicalTo('42'))
            ->willReturn($item);

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit('42');

        $view = $form->createView();

        self::assertArrayHasKey('item', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertSame($item, $view->vars['item']);
        self::assertSame('value', $view->vars['item_type']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendMock
            ->expects(self::once())
            ->method('loadItem')
            ->with(self::identicalTo('42'))
            ->willThrowException(new NotFoundException());

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit('42');

        $view = $form->createView();

        self::assertArrayHasKey('item', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertNull($view->vars['item']);
        self::assertSame('value', $view->vars['item_type']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithEmptyData(): void
    {
        $this->backendMock
            ->expects(self::never())
            ->method('loadItem');

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit(null);

        $view = $form->createView();

        self::assertArrayHasKey('item', $view->vars);
        self::assertArrayHasKey('item_type', $view->vars);

        self::assertNull($view->vars['item']);
        self::assertSame('value', $view->vars['item_type']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
            ]
        );

        self::assertSame($options['item_type'], 'value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(TextType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        self::assertSame('ngcb', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        return new ContentBrowserType($backendRegistry);
    }
}
