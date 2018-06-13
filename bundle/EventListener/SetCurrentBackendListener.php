<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Registry\BackendRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetCurrentBackendListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    private $backendRegistry;

    public function __construct(ContainerInterface $container, BackendRegistryInterface $backendRegistry)
    {
        $this->container = $container;
        $this->backendRegistry = $backendRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Injects the current backend into container.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        if (!$attributes->has('itemType')) {
            return;
        }

        $backend = $this->backendRegistry->getBackend($attributes->get('itemType'));
        $this->container->set('netgen_content_browser.current_backend', $backend);
    }
}
