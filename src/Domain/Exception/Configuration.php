<?php

namespace WakeOnWeb\ErrorsExtraLibrary\Domain\Exception;

class Configuration
{
    private $forceFormat;
    private $httpStatusCodes;
    private $messages;

    /**
     * @var string[]    key : FQCN, value : PSR-3 log level
     */
    private $logLevels;

    /**
     * Configuration constructor.
     *
     * @param string|null $forceFormat
     * @param array $httpStatusCodes
     * @param array $messages
     * @param array $logLevels
     */
    public function __construct($forceFormat = null, array $httpStatusCodes = [], array $messages = [], array $logLevels = [])
    {
        $this->forceFormat       = $forceFormat;
        $this->httpStatusCodes   = $httpStatusCodes;
        $this->messages          = $messages;
        $this->logLevels         = $logLevels;
    }

    public function getForcedFormat()
    {
        return $this->forceFormat;
    }

    /**
     * @param \Exception $exception exception
     *
     * @return integer
     */
    public function getHttpStatusCode(\Exception $exception)
    {
        foreach ($this->httpStatusCodes as $class => $code) {
            if ($exception instanceof $class) {
                return $code;
            }
        }

        return 500;
    }

    /**
     * @param \Exception $exception exception
     *
     * @return boolean
     */
    public function showsExceptionMessage(\Exception $exception)
    {
        foreach ($this->messages as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the custom log level for a given exception (if any)
     *
     * @param \Exception $exception
     *
     * @return string|null   PSR-3 log level name
     */
    public function getCustomLogLevel(\Exception $exception)
    {
        foreach ($this->logLevels as $fqcn => $level) {
            if ($exception instanceof $fqcn) {
                return $level;
            }
        }

        return null;
    }
}
