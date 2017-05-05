<?php

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Config\ConfigLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCurrentConfigListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigLoaderInterface
     */
    protected $configLoader;

    /**
     * Constructor.
     *
     * @param \Netgen\ContentBrowser\Config\ConfigLoaderInterface $configLoader
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
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $attributes = $request->attributes;

        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        if (!$attributes->has('itemType')) {
            return;
        }

        $itemType = $attributes->get('itemType');
        $configName = $request->query->get('configName', $itemType);

        $config = $this->configLoader->loadConfig($itemType, $configName);

        $this->container->set('netgen_content_browser.current_config', $config);
    }
}
