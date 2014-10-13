<?php

namespace Inneair\Synapps\IO;

use Inneair\Synapps\Exception\OutputBufferingException;

/**
 * This interface describes the services available in an output buffer, with support of PHP parsing on-the-fly, and
 * nested buffers (for a given implementation only).
 */
interface OutputBufferInterface
{
    /**
     * Cleans the output buffer.
     *
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function clean();

    /**
     * Closes the output buffer so as it cannot be used anymore.
     *
     * @throws OutputBufferingException If the output buffer is already closed or cannot be deleted.
     */
    public function close();

    /**
     * Flushes (sends) the output buffer.
     *
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function flush();

    /**
     * Gets the content in the output buffer.
     *
     * @return string Output buffer content.
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function get();

    /**
     * Gets the length of the content in the output buffer.
     *
     * @return int Number of characters.
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function getLength();

    /**
     * Gets the level of this output buffer in the output buffers stack.
     *
     * @return int Level (1 for the first output buffer opened).
     * @throws OutputBufferingException If the output buffer is already closed.
     */
    public function getLevel();

    /**
     * Tells whether this output buffer is closed.
     *
     * @return bool <code>true</code> if this output buffer is closed, <code>false</code> otherwise.
     */
    public function isClosed();
}
