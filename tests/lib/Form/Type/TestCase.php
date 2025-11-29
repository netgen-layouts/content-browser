<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class TestCase extends BaseTestCase
{
    final protected FormConfigBuilder $builder;

    final protected MockObject&EventDispatcherInterface $dispatcher;

    final protected FormTypeInterface $formType;

    final protected MockObject&ValidatorInterface $validatorMock;

    final protected FormFactoryInterface $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formType = $this->getMainType();

        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validatorMock
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addTypeExtension(new FormTypeValidatorExtension($this->validatorMock))
            ->getFormFactory();

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormConfigBuilder('name', null, $this->dispatcher);
        $this->builder->setFormFactory($this->factory);
    }

    abstract protected function getMainType(): FormTypeInterface;
}
