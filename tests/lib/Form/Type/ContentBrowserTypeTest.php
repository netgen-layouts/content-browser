<?php

namespace Netgen\ContentBrowser\Tests\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->backendMock = $this->createMock(BackendInterface::class);

        $backendRegistry = new BackendRegistry();
        $backendRegistry->addBackend('value', $this->backendMock);

        return new ContentBrowserType($backendRegistry);
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            array(
                'item_type' => 'value',
            )
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
            array(
                'item_type' => 'value',
            )
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
            array(
                'item_type' => 'value',
            )
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
            array(
                'item_type' => 'value',
            )
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
            array(
                'item_type' => 'value',
            )
        );

        $this->assertEquals($options['item_type'], 'value');
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array());
    }

    /**
     * @covers \Netgen\ContentBrowser\Form\Type\ContentBrowserType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidItemType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('item_type' => 42));
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
