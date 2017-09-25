<?php

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Netgen\ContentBrowser\Exceptions\InvalidArgumentException;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Exceptions\OutOfBoundsException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ExceptionConversionListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $exceptionMap = array(
        NotFoundException::class => NotFoundHttpException::class,
        OutOfBoundsException::class => UnprocessableEntityHttpException::class,
        InvalidArgumentException::class => BadRequestHttpException::class,
        // Various other useful exceptions
        AccessDeniedException::class => AccessDeniedHttpException::class,
    );

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array('onException', 10));
    }

    /**
     * Converts exceptions to Symfony HTTP exceptions.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        $exception = $event->getException();
        if ($exception instanceof HttpExceptionInterface) {
            return;
        }

        $exceptionClass = null;
        foreach ($this->exceptionMap as $sourceException => $targetException) {
            if (is_a($exception, $sourceException, true)) {
                $exceptionClass = $targetException;
                break;
            }
        }

        if ($exceptionClass !== null) {
            $convertedException = new $exceptionClass(
                $exception->getMessage(),
                $exception,
                $exception->getCode()
            );

            $event->setException($convertedException);
        }
    }
}
