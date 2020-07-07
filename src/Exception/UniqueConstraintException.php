<?php

namespace Inneair\Synapps\Exception;

use Exception;
use RuntimeException;

/**
 * Exception thrown when there is an attempt to create an object with a property containing a value already used, and
 * breaking a unique constraint.
 */
class UniqueConstraintException extends RuntimeException
{
    /**
     * The property's name.
     * @var string
     */
    private $property;

    /**
     * Creates an exception based on the given parameters.
     *
     * @param string $property Property's name (defaults to <code>null</code>).
     * @param string $message Message (defaults to <code>null</code>).
     * @param string $code Custom error code (defaults to <code>null</code>).
     * @param Exception $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct($property = null, $message = null, $code = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->property = $property;
    }

    /**
     * Gets the property's name that caused this exception.
     *
     * @return string Property's name.
     */
    public function getProperty()
    {
        return $this->property;
    }
}
