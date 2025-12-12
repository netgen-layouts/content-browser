<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;

abstract class TestCase extends BaseTestCase
{
    final protected FormConfigBuilder $builder;

    final protected Stub&EventDispatcherInterface $dispatcherStub;

    final protected FormTypeInterface $formType;

    final protected FormFactoryInterface $factory;

    final protected function setUp(): void
    {
        parent::setUp();

        $this->formType = $this->getMainType();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->getFormFactory();

        $this->dispatcherStub = self::createStub(EventDispatcherInterface::class);
        $this->builder = new FormConfigBuilder('name', null, $this->dispatcherStub);
        $this->builder->setFormFactory($this->factory);
    }

    abstract protected function getMainType(): FormTypeInterface;
}
