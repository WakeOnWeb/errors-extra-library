<?php

namespace WakeOnWeb\ErrorsExtraLibrary\Infra;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use WakeOnWeb\ErrorsExtraLibrary\Domain\Exception\Configuration;

class ExceptionListener
{
    /** @var Configuration */
    private $configuration;

    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $debug;

    /**
     * @param Configuration       $configuration configuration
     * @param LoggerInterface     $logger        logger
     * @param bool                $debug         debug
     */
    public function __construct(Configuration $configuration, LoggerInterface $logger = null, $debug = false)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();
        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : $this->configuration->getHttpStatusCode($exception);

        $format = $this->configuration->getForcedFormat() ?: $request->getRequestFormat();
        if ('json' !== $format) {
            return;
        }

        // Log as on Symfony\Component\HttpKernel\EventListener\ExceptionListener
        $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        $exceptionViewData = [
            'code' => $code,
            'message' => $this->guessExceptionMessage($exception, $code),
        ];

        $event->setResponse(new JsonResponse($exceptionViewData, $code));
    }

    /**
     * @param \Exception $e
     * @param int        $code
     *
     * @return string
     */
    private function guessExceptionMessage(\Exception $e, $code)
    {
        if ($this->debug || $this->configuration->showsExceptionMessage($e)) {
            return $e->getMessage();
        }

        return isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '';
    }

    /**
     * Logs the exception based on the configuration with the given message.
     *
     * @param \Exception $exception
     * @param string     $message
     */
    private function logException(\Exception $exception, $message)
    {
        if (!$this->logger instanceof LoggerInterface) {
            return;
        }

        // Custom level ?
        $level = $this->configuration->getCustomLogLevel($exception);
        // Based on the HTTP status code ?
        if (null === $level && $exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            $level = LogLevel::ERROR;
        }
        // Default
        $level = $level ?: LogLevel::CRITICAL;

        // Logging
        $this->logger->log($level, $message, ['exception' => $exception]);
    }
}
