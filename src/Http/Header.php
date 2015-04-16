<?php

namespace Inneair\Synapps\Http;

/**
 * This class contains HTTP header names.
 */
final class Header
{
    // General headers
    /**
     * Content-Type header.
     * @var string
     */
    const CONTENT_TYPE = 'Content-Type';

    // Request headers
    /**
     * Accept header.
     * @var string
     */
    const ACCEPT = 'Accept';
    /**
     * Accept-Encoding header.
     * @var string
     */
    const ACCEPT_ENCODING = 'Accept-Encoding';
    /**
     * Accept-Language header.
     * @var string
     */
    const ACCEPT_LANGUAGE = 'Accept-Language';
    /**
     * Authorization header.
     * @var string
     */
    const AUTHORIZATION = 'Authorization';

    // Response headers
    /**
     * Location header.
     * @var string
     */
    const LOCATION = 'Location';
    /**
     * WWW-Authenticate header.
     * @var string
     */
    const WWW_AUTHENTICATE = 'WWW-Authenticate';
    /**
     * Status header.
     * @var string
     */
    const STATUS = 'Status';

    /**
     * Private constructor prevents erroneous instantiations.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
