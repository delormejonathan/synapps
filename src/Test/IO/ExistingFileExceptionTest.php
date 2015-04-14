<?php

namespace Inneair\Synapps\Test\IO;

use Inneair\Synapps\IO\ExistingFileException;
use Inneair\Synapps\Test\AbstractSynappsTest;

/**
 * Class containing test suite for the {@link ExistingFileException} exception.
 */
class ExistingFileExceptionTest extends AbstractSynappsTest
{
    /**
     * A path.
     * @var string
     */
    const FILE_PATH = 'path';

    /**
     * Throws an exception with a file path.
     */
    public function testFilePath()
    {
        $hasException = false;
        try
        {
            throw new ExistingFileException(static::FILE_PATH);
        } catch (ExistingFileException $e) {
            $this->assertEquals(static::FILE_PATH, $e->getFilePath());
            $hasException = true;
        }
        $this->assertException($hasException);
    }
}
