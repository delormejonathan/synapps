<?php

namespace Inneair\Synapps\IO;

use ZipArchive;

/**
 * This class provides utilities for Zip archives management.
 */
final class ZipUtils
{
    /**
     * Prevents unwanted instantiations.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Extracts the content of a Zip archive into a directory.
     *
     * If the target directory does not exist, it will be created.
     *
     * @param File $zipFile Zip file.
     * @param File $targetDirectory Target directory.
     * @throws IOException If the Zip archive cannot be opened, or the content cannot be extracted.
     */
    public static function extractTo(File $zipFile, File $targetDirectory)
    {
        $zipArchive = new ZipArchive();
        $result = $zipArchive->open($zipFile->getOsPath(), ZipArchive::CHECKCONS);
        if ($result !== true) {
            throw new IOException('Cannot open ZIP archive (error ' . $result . '): ' . $zipFile->getPath());
        }
        try {
            if (!@$zipArchive->extractTo($targetDirectory->getOsPath())) {
                throw new IOException('Cannot extract content of ZIP archive: ' . $zipFile->getPath());
            }
        } finally {
            $zipArchive->close();
        }
    }
}
