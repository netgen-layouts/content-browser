<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Event\ConfigLoadEvent;
use Netgen\ContentBrowser\Event\ContentBrowserEvents;
use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetCurrentConfigListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    /**
     * Injects the current config into container.
     */
    public function onKernelRequest(GetResponseEvent $event): void
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

        $customParams = $request->query->get('customParams', []);
        if (!is_array($customParams)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid custom parameters specification for "%s" item type.',
                    $attributes->get('itemType')
                )
            );
        }

        $config->addParameters($customParams);

        $configLoadEvent = new ConfigLoadEvent($config);
        $this->eventDispatcher->dispatch(ContentBrowserEvents::CONFIG_LOAD, $configLoadEvent);

        $this->container->set('netgen_content_browser.current_config', $config);
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
                    $itemType
                )
            );
        }

        $config = $this->container->get($service);
        if (!$config instanceof Configuration) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration for "%s" item type is invalid.',
                    $itemType
                )
            );
        }

        return $config;
    }
}
