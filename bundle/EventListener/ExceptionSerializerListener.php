<?php

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSerializerListener implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    protected $outputDebugInfo = false;

    /**
     * Sets if the output should contain debugging information.
     *
     * @param bool $outputDebugInfo
     */
    public function setOutputDebugInfo($outputDebugInfo = false)
    {
        $this->outputDebugInfo = (bool) $outputDebugInfo;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Must happen BEFORE Symfony Security component ExceptionListener
        return array(KernelEvents::EXCEPTION => array('onException', 5));
    }

    /**
     * Serializes the exception.
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

        $data = array(
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        );

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            if (isset(Response::$statusTexts[$statusCode])) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $debugException = $exception;
            if ($exception->getPrevious() instanceof Exception) {
                $debugException = $exception->getPrevious();
            }

            $data['debug'] = array(
                'file' => $debugException->getFile(),
                'line' => $debugException->getLine(),
                'trace' => $debugException->getTrace(),
            );
        }

        $event->setResponse(new JsonResponse($data));
        $event->stopPropagation();
    }
}
