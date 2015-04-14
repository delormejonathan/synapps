<?php

namespace Inneair\Synapps\Test\IO;

use Inneair\Synapps\IO\File;
use Inneair\Synapps\IO\FileUtils;
use Inneair\Synapps\IO\IOException;
use Inneair\Synapps\IO\ZipUtils;
use Inneair\Synapps\Test\AbstractSynappsTest;
use ZipArchive;
use Inneair\Synapps\Util\StringUtils;

/**
 * Class containing test suite for Zip utilities.
 */
class ZipUtilsTest extends AbstractSynappsTest
{
    /**
     * Path to a file in a Zip archive.
     * @var string
     */
    const FILE_PATH = 'file';
    /**
     * Path to an extraction directory.
     * @var string
     */
    const TARGET_DIR_PATH = 'directory';
    /**
     * Path to a Zip file.
     * @var string
     */
    const ZIP_FILE_PATH = 'file.zip';

    public function clean()
    {
        parent::clean();

        FileUtils::deleteFile($this->getDataPathPrefix() . static::ZIP_FILE_PATH, true);
        FileUtils::deleteFile($this->getDataPathPrefix() . static::TARGET_DIR_PATH, true);
    }

    /**
     * Extracts an archive.
     */
    public function testExtractTo()
    {
        $zipFile = new File($this->getDataPathPrefix() . static::ZIP_FILE_PATH);
        $targetDirectory = new File($this->getDataPathPrefix() . static::TARGET_DIR_PATH);
    
        // Create a Zip archive.
        $zipArchive = new ZipArchive();
        try {
            if ($zipArchive->open($zipFile->getOsPath(), ZipArchive::OVERWRITE) !== true) {
                throw new IOException();
            }
            if (!$zipArchive->addFromString(static::FILE_PATH, StringUtils::EMPTY_STR)) {
                throw new IOException();
            }
        } finally {
            $zipArchive->close();
        }
    
        ZipUtils::extractTo($zipFile, $targetDirectory);
        $this->assertTrue($targetDirectory->exists());
        $inclosedFile = new File($targetDirectory->getPath() . File::DIRECTORY_SEPARATOR . static::FILE_PATH);
        $this->assertTrue($inclosedFile->exists());
    }

    /**
     * Extracts an archive to an unknown directory.
     */
    public function testExtractToUnknownDirectory()
    {
        $zipFile = new File($this->getDataPathPrefix() . static::ZIP_FILE_PATH);
        $targetDirectory = new File('abcd://' . $this->getDataPathPrefix() . static::TARGET_DIR_PATH);

        // Create a Zip archive.
        $zipArchive = new ZipArchive();
        try {
            if ($zipArchive->open($zipFile->getOsPath(), ZipArchive::OVERWRITE) !== true) {
                throw new IOException();
            }
            if (!$zipArchive->addFromString(static::FILE_PATH, 'test')) {
                throw new IOException();
            }
        } finally {
            $zipArchive->close();
        }

        $hasException = false;
        try {
            ZipUtils::extractTo($zipFile, $targetDirectory);
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Extracts an unknown archive.
     */
    public function testExtractUnknownArchive()
    {
        $zipFile = new File(static::ZIP_FILE_PATH);
        $targetDirectory = new File(static::TARGET_DIR_PATH);

        $hasException = false;
        try {
            ZipUtils::extractTo($zipFile, $targetDirectory);
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }
}
