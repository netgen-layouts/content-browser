<?php

namespace Netgen\Bundle\ContentBrowserBundle\ParamConverter;

use Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface;
use Netgen\Bundle\ContentBrowserBundle\Value\ValueInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class ValueParamConverter implements ParamConverterInterface
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface
     */
    protected $valueLoaderRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     */
    public function __construct(ValueLoaderRegistryInterface $valueLoaderRegistry)
    {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
    }

    /**
     * Stores the object in the request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request The request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverterConfiguration $configuration)
    {
        if (!$request->attributes->has('valueId') || !$request->attributes->has('valueType')) {
            return false;
        };

        $valueId = $request->attributes->get('valueId');
        // 0 is a valid value ID
        if ($valueId === null || $valueId === "") {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new UnexpectedValueException('Required request attribute "valueId" is empty');
        }

        $valueLoader = $this->valueLoaderRegistry->getValueLoader($request->attributes->get('valueType'));
        $request->attributes->set('value', $valueLoader->load($valueId));

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverterConfiguration $configuration)
    {
        return is_a($configuration->getClass(), ValueInterface::class, true);
    }
}
