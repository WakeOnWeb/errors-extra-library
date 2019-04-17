<?php

namespace WakeOnWeb\ErrorsExtraLibrary\Domain\Formatter;

/**
 * Interface ResponseFormatterInterface
 *
 * @author Olivier Maréchal <o.marechal@wakeonweb.com>
 */
interface ResponseFormatterInterface
{
    /**
     * Format response
     *
     * @param \Exception $e
     * @param int        $code
     * @param string     $message
     *
     * @return array
     */
    public function format(\Exception $e, int $code, string $message): array;
}

