<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetBackendListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    private ContainerInterface $container;

    private BackendRegistry $backendRegistry;

    public function __construct(ContainerInterface $container, BackendRegistry $backendRegistry)
    {
        $this->container = $container;
        $this->backendRegistry = $backendRegistry;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 1]];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event): void
    {
        if (!$this->isMainRequest($event)) {
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
