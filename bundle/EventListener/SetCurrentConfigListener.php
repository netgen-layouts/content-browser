<?php

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetCurrentConfigListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

        $config = $this->loadConfig($attributes->get('itemType'));

        $customParams = (array) $request->query->get('customParams', array());
        $config->addParameters($customParams);

        $this->container->set('netgen_content_browser.current_config', $config);
    }

    /**
     * Loads the configuration for provided item type from the container.
     *
     * @param string $itemType
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config could not be found
     *
     * @return \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    private function loadConfig($itemType)
    {
        $service = 'netgen_content_browser.config.' . $itemType;

        if (!$this->container->has($service)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" item type does not exist.',
                    $itemType
                )
            );
        }

        return $this->container->get($service);
    }
}
