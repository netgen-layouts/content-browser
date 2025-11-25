<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Event\ConfigLoadEvent;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class SetConfigListener implements EventSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container,
        private ConfigRegistry $configRegistry,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onKernelRequest', 1]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
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

        $config = $this->configRegistry->getConfig($attributes->get('itemType'));

        $customParams = $request->query->all('customParams');
        $config->addParameters($customParams);

        $this->eventDispatcher->dispatch(new ConfigLoadEvent($config));

        $this->container->set('netgen_content_browser.config', $config);
    }
}
