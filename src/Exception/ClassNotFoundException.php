<?php

namespace Inneair\Synapps\Exception;

use RuntimeException;

/**
 * Exception thrown when a class cannot be found.
 */
class ClassNotFoundException extends RuntimeException
{
    /**
     * The class name.
     * @var string
     */
    private $className;

    /**
     * Creates an exception based on the given parameters.
     *
     * {@inheritdoc}
     *
     * @param string $className Class name.
     * @param string $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct($className, $previous = null)
    {
        parent::__construct('Class \'' . $className . '\' not found', null, $previous);
        $this->className = $className;
    }

    /**
     * Gets the class name that caused this exception.
     *
     * @return string Class name.
     */
    public function getClassName()
    {
        return $this->className;
    }
}
