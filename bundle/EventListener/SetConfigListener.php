<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Event\ConfigLoadEvent;
use Netgen\ContentBrowser\Event\ContentBrowserEvents;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function sprintf;

final class SetConfigListener implements EventSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 1]];
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

        $config = $this->loadConfig($attributes->get('itemType'));

        $customParams = $request->query->all('customParams');
        $config->addParameters($customParams);

        $configLoadEvent = new ConfigLoadEvent($config);

        $this->eventDispatcher->dispatch($configLoadEvent, ContentBrowserEvents::CONFIG_LOAD);

        $this->container->set('netgen_content_browser.config', $config);
    }

    /**
     * Loads the configuration for provided item type from the container.
     *
     * @throws \Netgen\ContentBrowser\Exceptions\InvalidArgumentException If config could not be found
     */
    private function loadConfig(string $itemType): Configuration
    {
        $service = 'netgen_content_browser.config.' . $itemType;

        if (!$this->container->has($service)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" item type does not exist.',
                    $itemType,
                ),
            );
        }

        $config = $this->container->get($service);
        if (!$config instanceof Configuration) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" item type is invalid.',
                    $itemType,
                ),
            );
        }

        return $config;
    }
}
