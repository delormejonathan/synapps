<?php

namespace Inneair\Synapps\IO;

/**
 * Exception thrown when there is an attempt to create a file/directory/symbolic link that already exists (potentially,
 * with a different case).
 */
class ExistingFileException extends IOException
{
    /**
     * File path.
     * @var string
     */
    private $filePath;

    /**
     * Creates an exception based on the given parameters.
     *
     * {@inheritDoc}
     *
     * @param string $filePath File path.
     * @param string $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct($filePath, $previous = null)
    {
        parent::__construct('The file \'' . $filePath . '\' already exists', null, $previous);
        $this->filePath = $filePath;
    }

    /**
     * Gets the file path that caused this exception.
     *
     * @return string File path.
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
