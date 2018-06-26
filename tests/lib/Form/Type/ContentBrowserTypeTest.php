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
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $backendMock;

    public function getMainType(): FormTypeInterface
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry(['value' => $this->backendMock]);

        return new ContentBrowserType($backendRegistry, ['value' => 'Value']);
    }

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

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('42', $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildView(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item(42)));

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit('42');

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('item_name', $view->vars);

        $this->assertSame('value', $view->vars['item_type']);
        $this->assertSame('This is a name (42)', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithNonExistingItem(): void
    {
        $this->backendMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            [
                'item_type' => 'value',
            ]
        );

        $form->submit('42');

        $view = $form->createView();

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('item_name', $view->vars);

        $this->assertSame('value', $view->vars['item_type']);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithEmptyData(): void
    {
        $this->backendMock
            ->expects($this->never())
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

        $this->assertArrayHasKey('item_type', $view->vars);
        $this->assertArrayHasKey('item_name', $view->vars);

        $this->assertSame('value', $view->vars['item_type']);
        $this->assertNull($view->vars['item_name']);
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

        $this->assertSame($options['item_type'], 'value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::getParent
     */
    public function testGetParent(): void
    {
        $this->assertSame(TextType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertSame('ng_content_browser', $this->formType->getBlockPrefix());
    }
}
