<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Form\Type;

use Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentBrowserTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRepositoryMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);

        return new ContentBrowserType($this->itemRepositoryMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildView()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Item()));

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            array(
                'value_type' => 'value',
                'config_name' => 'config',
            )
        );

        $form->submit(42);

        $view = $form->createView();

        self::assertArrayHasKey('value_type', $view->vars);
        self::assertArrayHasKey('config_name', $view->vars);
        self::assertArrayHasKey('item_name', $view->vars);

        self::assertEquals('value', $view->vars['value_type']);
        self::assertEquals('config', $view->vars['config_name']);
        self::assertEquals('This is a name', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithNonExistingItem()
    {
        $this->itemRepositoryMock
            ->expects($this->once())
            ->method('loadItem')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException()));

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            array(
                'value_type' => 'value',
                'config_name' => 'config',
            )
        );

        $form->submit(42);

        $view = $form->createView();

        self::assertArrayHasKey('value_type', $view->vars);
        self::assertArrayHasKey('config_name', $view->vars);
        self::assertArrayHasKey('item_name', $view->vars);

        self::assertEquals('value', $view->vars['value_type']);
        self::assertEquals('config', $view->vars['config_name']);
        self::assertEquals('(INVALID ITEM)', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::buildView
     */
    public function testBuildViewWithEmptyData()
    {
        $this->itemRepositoryMock
            ->expects($this->never())
            ->method('loadItem');

        $form = $this->factory->create(
            ContentBrowserType::class,
            null,
            array(
                'value_type' => 'value',
                'config_name' => 'config',
            )
        );

        $form->submit(null);

        $view = $form->createView();

        self::assertArrayHasKey('value_type', $view->vars);
        self::assertArrayHasKey('config_name', $view->vars);
        self::assertArrayHasKey('item_name', $view->vars);

        self::assertEquals('value', $view->vars['value_type']);
        self::assertEquals('config', $view->vars['config_name']);
        self::assertEquals('(NO ITEM SELECTED)', $view->vars['item_name']);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'value_type' => 'value',
                'config_name' => 'test',
            )
        );

        self::assertEquals($options['value_type'], 'value');
        self::assertEquals($options['config_name'], 'test');
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingValueType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('config_name' => 'test'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingConfigName()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('value_type' => 'value'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidValueType()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('value_type' => 42, 'config_name' => 'test'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidConfigName()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(array('value_type' => 'value', 'config_name' => 42));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        self::assertEquals('ng_content_browser', $this->formType->getBlockPrefix());
    }
}
