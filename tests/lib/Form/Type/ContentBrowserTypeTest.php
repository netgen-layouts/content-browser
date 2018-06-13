<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentBrowserTypeTest extends TestCase
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

        return new ContentBrowserType($backendRegistry, ['value' => 'Value']);
    }

    public function testSubmitValidData()
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
        $this->assertEquals(42, $form->getData());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::__construct
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildView()
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

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertEquals('This is a name (42)', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithNonExistingItem()
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

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithEmptyData()
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

        $this->assertEquals('value', $view->vars['item_type']);
        $this->assertNull($view->vars['item_name']);
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'item_type' => 'value',
            ]
        );

        $this->assertEquals($options['item_type'], 'value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
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
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::getParent
     */
    public function testGetParent()
    {
        $this->assertEquals(TextType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ng_content_browser', $this->formType->getBlockPrefix());
    }
}
