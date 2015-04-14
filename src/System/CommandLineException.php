<?php

namespace Inneair\Synapps\System;

use Exception;
use RuntimeException;

/**
 * Base exception thrown when a command line is malformed, or contains invalid parameters.
 */
class CommandLineException extends RuntimeException
{
    /**
     * Creates an exception based on the given parameters.
     *
     * {@inheritdoc}
     *
     * @param string $message Exception message (defaults to <code>null</code>).
     * @param Exception $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct($message = null, $previous = null)
    {
        parent::__construct($message, null, $previous);
    }
}
