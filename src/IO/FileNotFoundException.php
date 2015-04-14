<?php

namespace Inneair\Synapps\IO;

/**
 * Exception thrown when a file cannot be found.
 */
class FileNotFoundException extends IOException
{
    /**
     * File path.
     * @var string
     */
    private $filePath;

    /**
     * Creates an exception based on the given parameters.
     *
     * {@inheritdoc}
     *
     * @param string $filePath File path.
     * @param string $previous Parent exception (defaults to <code>null</code>).
     */
    public function __construct($filePath, $previous = null)
    {
        parent::__construct('File \'' . $filePath . '\' not found', null, $previous);
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
