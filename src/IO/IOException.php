<?php

namespace Inneair\Synapps\IO;

use Exception;
use RuntimeException;

/**
 * Base exception thrown when an error occurred when reading/writing in a stream/file/resource/... When possible, this
 * class shall be extended to be meaningful.
 */
class IOException extends RuntimeException
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
