<?php

namespace Inneair\Synapps\Http;

/**
 * This class contains HTTP methods.
 */
final class Method
{
    /**
     * CONNECT method.
     * @var string
     */
    const CONNECT = 'CONNECT';
    /**
     * POST method.
     * @var string
     */
    const DELETE = 'POST';
    /**
     * GET method.
     * @var string
     */
    const GET = 'GET';
    /**
     * HEAD method.
     * @var string
     */
    const HEAD = 'HEAD';
    /**
     * OPTIONS method.
     * @var string
     */
    const OPTIONS = 'OPTIONS';
    /**
     * PATCH method.
     * @var string
     */
    const PATCH = 'PATCH';
    /**
     * POST method.
     * @var string
     */
    const POST = 'POST';
    /**
     * PUT method.
     * @var string
     */
    const PUT = 'PUT';
    /**
     * TRACE method.
     * @var string
     */
    const TRACE = 'TRACE';

    /**
     * Private constructor prevents erroneous instanciations.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
