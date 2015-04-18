<?php

namespace Organizer\Bundle\RestBundle\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class JsonResponse
 * @package Organizer\Bundle\RestBundle\Http
 *
 * Wrapper for Symfony response for a Json Response
 * Unlike the Symfony JsonResponse, the class takes
 * a string which is already json
 */
class JsonResponse extends Response
{
    /**
     * @param string $json
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct($json, $statusCode=200, $headers=[])
    {
        $this->content = $json;
        $this->statusCode = $statusCode;
        $this->headers = new ResponseHeaderBag(array_merge($headers, ['Content-type' => 'application/json']));
    }
} 