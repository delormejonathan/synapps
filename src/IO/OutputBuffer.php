<?php

namespace Inneair\Synapps\IO;

use Inneair\Synapps\Exception\OutputBufferingException;

/**
 * This class is a wrapper of the native output buffering utilities in PHP, to provide object-oriented access to output
 * buffering. Originally, this class was needed to be able to mock such functions, which is not possible with native
 * PHP.
 */
class OutputBuffer
{
    /**
     * If the output buffer is closed and unusable anymore.
     * @var bool
     */
    private $closed;

    /**
     * Builds an output buffer and turn it on (no implicit flush).
     *
     * @param callable $outputCallback See ob_start.
     * @param int $chunkSize See ob_start.
     * @param int $flags See ob_start.
     * @throws OutputBufferingException If the output buffer cannot be turned on.
     */
    public function __construct($outputCallback = null, $chunkSize = 0, $flags = PHP_OUTPUT_HANDLER_STDFLAGS)
    {
        $result = @ob_start($outputCallback, $chunkSize, $flags);
        if (!$result) {
            throw new OutputBufferingException('Cannot start output buffering.');
        }
        $this->closed = false;
    }

    /**
     * Checks if this output buffer is closed, and throw an exception in this case.
     *
     * @throws OutputBufferingException If the output buffer is closed.
     */
    private function checkClosed()
    {
        if ($this->isClosed()) {
            throw new OutputBufferingException('Output buffer closed.');
        }
    }

    /**
     * Cleans the output buffer.
     *
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function clean()
    {
        $this->checkClosed();
        ob_clean();
    }

    /**
     * Closes the output buffer so as it cannot be used anymore.
     *
     * @throws OutputBufferingException If the output buffer is already closed or cannot be deleted.
     */
    public function close()
    {
        $this->checkClosed();
        if (!ob_end_clean()) {
            throw new OutputBufferingException('Output buffer cannot be closed.');
        }
        $this->closed = true;
    }

    /**
     * Flushes (sends) the output buffer.
     *
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function flush()
    {
        $this->checkClosed();
        ob_flush();
    }

    /**
     * Gets the content in the output buffer.
     *
     * @return string Output buffer content.
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function get()
    {
        $this->checkClosed();
        $result = ob_get_contents();
        if ($result === false) {
            throw new OutputBufferingException('Output buffer closed.');
        }
        return $result;
    }

    /**
     * Gets the length of the content in the output buffer.
     *
     * @return int Number of characters.
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function getLength()
    {
        $this->checkClosed();
        $result = ob_get_length();
        if ($result === false) {
            throw new OutputBufferingException('Output buffer already closed.');
        }
        return $result;
    }

    /**
     * Gets the level of this output buffer in the output buffers stack.
     *
     * @return int Level (1 for the first output buffer opened).
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function getLevel()
    {
        $this->checkClosed();
        $result = ob_get_level();
        if ($result === 0) {
            throw new OutputBufferingException('Output buffer already closed.');
        }
        return $result;
    }

    /**
     * Gets the status of this output buffer.
     *
     * @return array An array of properties.
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function getStatus()
    {
        $this->checkClosed();
        $result = ob_get_status(true);
        if ($result === 0) {
            throw new OutputBufferingException('Output buffer already closed.');
        }
        return $result[$this->getLevel() - 1];
    }

    /**
     * Tells whether this output buffer is closed.
     *
     * @return bool <code>true</code> if this output buffer is closed, <code>false</code> otherwise.
     */
    public function isClosed()
    {
        return $this->closed;
    }
}
