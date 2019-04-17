<?php

namespace WakeOnWeb\ErrorsExtraLibrary\Infra\Formatter;

use WakeOnWeb\ErrorsExtraLibrary\Domain\Formatter\ResponseFormatterInterface;

/**
 * Class ResponseFormatter
 *
 * @author Olivier Maréchal <o.marechal@wakeonweb.com>
 */
class ResponseFormatter implements ResponseFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(\Exception $e, int $code, string $message): array
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }
}
