<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetBackendListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistry
     */
    private $backendRegistry;

    public function __construct(ContainerInterface $container, BackendRegistry $backendRegistry)
    {
        $this->container = $container;
        $this->backendRegistry = $backendRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

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
        $this->container->set('netgen_content_browser.backend', $backend);
    }
}