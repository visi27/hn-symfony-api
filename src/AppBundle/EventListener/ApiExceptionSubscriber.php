<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\EventListener;

use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use AppBundle\Api\ResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private $debug;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct($debug, LoggerInterface $logger, ResponseFactory $responseFactory)
    {
        $this->debug = $debug;
        $this->logger = $logger;
        $this->responseFactory = $responseFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // only reply to /api URLs
        if (mb_strpos($event->getRequest()->getPathInfo(), '/api') !== 0) {
            return;
        }

        $e = $event->getException();

        $this->logger->debug($e->getTraceAsString());

        $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        if ($statusCode === 500 && $this->debug) {
            return;
        }

        if ($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } else {
            $apiProblem = new ApiProblem($statusCode);
        }

        //If we have a HttpException add the detail key with the exceptions message as value
        if ($e instanceof HttpExceptionInterface) {
            $apiProblem->set('detail', $e->getMessage());
        }

        $response = $this->responseFactory->createResponse($apiProblem);

        $event->setResponse($response);
    }
}
