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
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Registry\ValueLoaderRegistryInterface $valueLoaderRegistry
     * @param array $config
     */
    public function __construct(ValueLoaderRegistryInterface $valueLoaderRegistry, array $config)
    {
        $this->valueLoaderRegistry = $valueLoaderRegistry;
        $this->config = $config;
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
        if (!$request->attributes->has('valueId')) {
            return false;
        };

        $valueId = $request->attributes->get('valueId');
        if (empty($valueId)) {
            if ($configuration->isOptional()) {
                return false;
            }

            throw new UnexpectedValueException('Required request attribute "valueId" is empty');
        }

        $valueLoader = $this->valueLoaderRegistry->getValueLoader($this->config['value_type']);
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
