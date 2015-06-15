<?php

namespace Inneair\Synapps\Test\IO;

use Inneair\Synapps\IO\File;
use Inneair\Synapps\IO\FileInputStream;
use Inneair\Synapps\IO\FileNotFoundException;
use Inneair\Synapps\IO\IOException;
use Inneair\Synapps\Test\AbstractSynappsTest;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;

/**
 * Class containing test suite for the {@link FileInputStream} class.
 */
class FileInputStreamTest extends AbstractSynappsTest
{
    /**
     * File content.
     * @var string
     */
    const CONTENT = '0123456789';
    /**
     * A file name.
     * @var string
     */
    const FILE_NAME = 'file';

    /**
     * Virtual root directory
     * @var vfsStreamDirectory
     */
    private $rootDirectory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->rootDirectory = vfsStream::setup();

        parent::setUp();
    }

    /**
     * Tests an exception is thrown when building an input stream over a non-existing file.
     */
    public function testBuildInputStreamWithNonExistingFile()
    {
        $hasException = false;
        try {
            new FileInputStream(new File(static::FILE_NAME));
        } catch (FileNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Tests an exception is thrown when building an input stream over a non-readable file.
     */
    public function testBuildInputStreamWithNonReadableFile()
    {
        $file = new vfsStreamFile(static::FILE_NAME);
        $file->chmod(0000);
        $this->rootDirectory->addChild($file);

        $hasException = false;
        try {
            new FileInputStream(new File($file->url()));
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Tests closing an input stream invalidates the file pointer, and that an exception is thrown for any subsequent
     * calls.
     */
    public function testCloseInputStream()
    {
        $file = new vfsStreamFile(static::FILE_NAME);
        $this->rootDirectory->addChild($file);

        $inputStream = null;
        try {
            $inputFile = new File($file->url());
            $inputStream = new FileInputStream($inputFile);
            $inputStream->close();
        } catch (Exception $e) {
            if ($inputStream !== null) {
                $inputStream->close();
            }
            throw $e;
        }
        $this->assertSame($inputFile, $inputStream->getFile());

        $hasException = false;
        try {
            $inputStream->close();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Tests reading lines from an input stream, and that an exception is thrown is the input stream was closed.
     */
    public function testReadInputStream()
    {
        $file = new vfsStreamFile(static::FILE_NAME);
        $file->setContent(static::CONTENT);
        $this->rootDirectory->addChild($file);

        $line1 = null;
        try {
            $inputStream = new FileInputStream(new File($file->url()));
            $line1 = $inputStream->readLine();
            $line2 = $inputStream->readLine();
            $inputStream->close();
        } catch (Exception $e) {
            if ($inputStream !== null) {
                $inputStream->close();
            }
            throw $e;
        }
        $this->assertSame(static::CONTENT, $line1);
        $this->assertNull($line2);

        $hasException = false;
        try {
            $inputStream->readLine();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }
}
