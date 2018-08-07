<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\Form\Type;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var \Symfony\Component\Form\FormConfigBuilder
     */
    protected $builder;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\Form\FormTypeInterface
     */
    protected $formType;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $validatorMock;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->formType = $this->getMainType();

        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validatorMock
            ->expects(self::any())
            ->method('validate')
            ->will(self::returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addTypes($this->getTypes())
            ->addTypeExtension(new FormTypeValidatorExtension($this->validatorMock))
            ->getFormFactory();

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormConfigBuilder('name', null, $this->dispatcher);
        $this->builder->setFormFactory($this->factory);
    }

    abstract public function getMainType(): FormTypeInterface;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes(): array
    {
        return [];
    }
}
