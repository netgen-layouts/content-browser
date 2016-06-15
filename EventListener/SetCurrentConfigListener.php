<?php

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCurrentConfigListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface
     */
    protected $configLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface $configLoader
     */
    public function __construct(ConfigLoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
    }

    /**
     * Injects the current config into container.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        if (!$attributes->has('config')) {
            return;
        }

        $config = $this->configLoader->loadConfig($attributes->get('config'));

        $this->container->set('netgen_content_browser.current_config', $config);
        $attributes->set('valueType', $config['value_type']);
    }
}
