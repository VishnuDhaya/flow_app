<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Monolog\Formatter\LineFormatter;

class FlowLogFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  Logger  $logger
     *
     * @return void
     */

    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(function ($record) {
                $record['extra'][] = session('log_prefix');
                return $record;
            });
            $handler->setFormatter(tap(new LineFormatter(
                "[%datetime%] %extra% %channel%.%level_name%: %message% %context% \n",
                'Y-m-d H:i:s',
                true,
                true
            ), function ($formatter) {
                $formatter->includeStacktraces();
            }));
        }
    }
}