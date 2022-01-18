<?php

namespace Payolution\Request\Log;

/**
 * Factory to create log messages from the response/request
 *
 * @package Payolution\Request\Log
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class LogMessageFactory
{
    /**
     * Request whitelist
     *
     * @var array
     */
    const REQUEST_WHITELIST = [
        'PAYMENT.CODE',
        'RANSACTION.MODE',
        'PRESENTATION.AMOUNT',
        'IDENTIFICATION.TRANSACTIONID'
    ];

    /**
     * Response whitelist
     *
     * @var array
     */
    const RESPONSE_WHITELIST = [
        'P3_VALIDATION',
        'PROCESSING_RETURN',
        'PROCESSING_RESULT',
        'PROCESSING_REASON_CODE'
    ];

    /**
     * Create truncated request log message
     *
     * @param array $request
     *
     * @param bool $debug
     *
     * @return array
     */
    public static function createFromRequest(array $request, $debug = false)
    {
        if (isset($request['body'])) {
            $request = $request['body'];
        }

        if (!$debug) {
            $result = self::getRelevantElements($request, self::REQUEST_WHITELIST);
        } else {
            $result = $request;
        }

        return $result;
    }

    /**
     * Create truncated response log message
     *
     * @param array $response
     * @param bool $debug
     *
     * @return array
     */
    public static function createFromResponse(array $response, $debug = false)
    {
        if (!$debug) {
            $result = self::getRelevantElements($response, self::RESPONSE_WHITELIST);
        } else {
            $result = $response;
        }

        return $result;
    }

    /**
     * Get relevant Elements
     *
     * @param array $input
     * @param $mapping
     * @return array
     */
    private static function getRelevantElements(array $input, $mapping)
    {
        $result = [];
        foreach ($input as $key => $value) {
            if (in_array($key, $mapping, true)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
