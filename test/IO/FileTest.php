<?php

namespace Inneair\Synapps\Test\IO;

use DateTime;
use Inneair\Synapps\IO\ExistingFileException;
use Inneair\Synapps\IO\File;
use Inneair\Synapps\IO\FileNotFoundException;
use Inneair\Synapps\IO\FileUtils;
use Inneair\Synapps\IO\IOException;
use Inneair\Synapps\Test\AbstractSynappsTest;

/**
 * Class containing test suite for the file class.
 */
class FileTest extends AbstractSynappsTest
{
    /**
     * Name of a file that does not exist.
     * @var string
     */
    const MISSING_FILENAME = 'a';
    /**
     * Name of an existing file.
     * @var string
     */
    const EXISTING_FILENAME = 'file1.txt';
    /**
     * Name of an existing directory.
     * @var string
     */
    const EXISTING_DIRECTORY_1 = 'directory1';
    /**
     * Name of another existing directory.
     * @var string
     */
    const EXISTING_DIRECTORY_2 = 'directory2';
    /**
     * A file name.
     * @var string
     */
    const FILE_NAME_1 = 'aäöü&()éèàâêûô@$+ç%=!£_;{[]}~´¢¬§°#@…€`z';
    /**
     * A file name.
     * @var string
     */
    const FILE_NAME_2 = 'äöü&()éèàâêûô@$+ç%=!£_;{[]}~´¢¬§°#@…€`';

    /**
     * Initializes environment for all test cases.
     */
    public static function setUpBeforeClass()
    {
        // This configuration setting is mandatory to ensure file names are correctly encoded when using special
        // characters. File related functions will not manage special characters if this setting is missing.
        mb_internal_encoding('UTF-8');
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        parent::clean();

         FileUtils::deleteFile($this->getDataPathPrefix() . self::FILE_NAME_1, true);
         FileUtils::deleteFile($this->getDataPathPrefix() . self::FILE_NAME_2, true);
         FileUtils::deleteFile(
            $this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1,
            true
         );
    }

    /**
     * Checks the method to copy a directory.
     */
    public function testCopyDirectory()
    {
        // Copies a directory into a missing parent directory.
        $srcFile = new File($this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1);
        $destFile = new File(
            $this->getDataPathPrefix() . self::FILE_NAME_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $hasException = false;
        try {
            $srcFile->copy($destFile);
        } catch (FileNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Copies a directory (case change only).
        $destFile = new File(
            $srcFile->getParentPath() . File::DIRECTORY_SEPARATOR . mb_strtoupper($srcFile->getName())
        );
        $hasException = false;
        try {
            $srcFile->copy($destFile);
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Copies a directory.
        $destFile = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $srcFile->copy($destFile);
        $this->assertContentEquals($srcFile, $destFile);
    }

    /**
     * Checks the method to copy a file.
     */
    public function testCopyFile()
    {
        // Copies a missing file.
        $srcFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
        $destFile = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $hasException = false;
        try {
            $srcFile->copy($destFile);
        } catch (IOException $e) {
            $this->assertFalse($e instanceof FileNotFoundException);
            $hasException = true;
        }
        $this->assertException($hasException);

        // Copies a file into a missing parent directory.
        $srcFile = new File($this->getDataPathPrefix() . self::EXISTING_FILENAME);
        $destFile = new File(
            $this->getDataPathPrefix() . self::FILE_NAME_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $hasException = false;
        try {
            $srcFile->copy($destFile);
        } catch (FileNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Copies a file into a file with a name in a different case.
        $destFile = new File(
            $srcFile->getParentPath() . File::DIRECTORY_SEPARATOR . mb_strtoupper($srcFile->getName())
        );
        $hasException = false;
        try {
            $srcFile->copy($destFile);
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Copies a file into a file with a name used by an existing directory.
        $destFile = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $destFile->createDirectory();
        $hasException = false;
        try {
            $srcFile->copy($destFile);
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
        $destFile->delete();

        // Copies a file.
        $srcFile->copy($destFile);
        $this->assertTrue($destFile->exists());
    }

    /**
     * Checks the method to create a directory.
     */
    public function testCreateDirectory()
    {
        // Creates a directory with a name already used by a file.
        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $hasException = false;
        $file->create();
        try {
            $file->createDirectory();
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
        $file->delete();

        $file = new File(
            $this->getDataPathPrefix() . self::FILE_NAME_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $this->assertFalse(is_dir($file->getOsPath()));
        $this->assertFalse($file->isDirectory());

        // Creates multiple directories without recursive mode.
        $hasException = false;
        try {
            $file->createDirectory();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates multiple directories with recursive mode.
        $result = $file->createDirectory(File::DEFAULT_MODE, true);
        $this->assertTrue($result);
        $this->assertTrue(is_dir($file->getOsPath()));
        $this->assertTrue($file->isDirectory());

        // Creates an existing directory.
        $result = $file->createDirectory();
        $this->assertFalse($result);
    }

    /**
     * Checks the method to create a file.
     */
    public function testCreateFile()
    {
        // Creates a file with a name already used by a directory.
        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $hasException = false;
        $file->createDirectory();
        try {
            $file->create();
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
        $file->delete();

        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1);
        $this->assertFalse(is_file($file->getOsPath()));
        $this->assertFalse($file->isFile());

        // Creates a file in a missing directory
        $hasException = false;
        try {
            $file->create();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates a directory
        $directory = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $directory->createDirectory();
        // Creates a file in the previous directory
        $file->create();
        $this->assertTrue(is_file($file->getOsPath()));
        $this->assertTrue($file->isFile());

        // Creates again an existing file.
        $hasException = false;
        try {
            $file->create();
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Checks the method to create a symbolic link.
     */
    public function testCreateSymbolicLink()
    {
        // Creates a symbolic link with a name already used by a file.
        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $hasException = false;
        $file->create();
        try {
            $file->createSymbolicLink($this->getDataPath());
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
        $file->delete();

        // Creates a symbolic link in a missing directory to an existing directory.
        $link = new File(
            $this->getDataPathPrefix() . self::FILE_NAME_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $this->assertFalse(is_link($link->getOsPath()));
        $this->assertFalse($link->isSymbolicLink());
        $hasException = false;
        try {
            $link->createSymbolicLink($this->getDataPath());
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates a symbolic link on a missing file/directory.
        $link = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $hasException = false;
        try {
            $link->createSymbolicLink(
                $this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
            );
        } catch (FileNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates a symbolic link on an existing directory.
        $directory = new File(
            $this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $directory->createDirectory();
        $link->createSymbolicLink($directory);
        $this->assertTrue(is_link($link->getOsPath()));
        $this->assertTrue($link->isSymbolicLink());
        $this->assertTrue($link->isDirectory());
        $this->assertEquals(realpath($directory->getOsPath()), File::normalizePath($link->getRealPath(), true, false));

        // Creates again a symbolic link already existing.
        $hasException = false;
        try {
            $link->createSymbolicLink($directory);
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);
    }

    /**
     * Checks the method to deleted a file/directory/symbolic link.
     */
    public function testDelete()
    {
        // Deletes a missing file.
        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $this->assertFalse($file->delete());

        // Deletes an existing file.
        $file->create();
        $this->assertDelete($file);

        // Deletes a symbolic link to a file.
        $target = new File(
            $this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $target->create();
        $file->createSymbolicLink($target);
        $this->assertDelete($file);

        // Deletes a symbolic link to a missing file.
        $file->createSymbolicLink($target);
        $target->delete();
        $this->assertDelete($file);

        // Deletes a symbolic link to a directory.
        $target->createDirectory();
        $file->createSymbolicLink($target);
        $this->assertDelete($file);

        // Deletes a symbolic link to a missing directory.
        $file->createSymbolicLink($target);
        $target->delete();
        $this->assertDelete($file);

        // Deletes an empty directory.
        $file->createDirectory();
        $this->assertDelete($file);

        // Deletes a non-empty directory (non-recursive).
        $file = new File($this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1);
        $dest = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $file->copy($dest);
        $hasException = false;
        try {
            $dest->delete();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Deletes a non-empty directory (recursive).
        $dest->delete(true);
        $this->assertFalse($dest->exists());
    }

    /**
     * Checks the method to get file content.
     */
    public function testGetContents()
    {
        // Reads content of a missing file.
        $hasException = false;
        try {
            $missingFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
            $missingFile->getContent();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        $originalContent = file_get_contents(
            File::encodeOsFileName($this->getDataPathPrefix() . self::EXISTING_FILENAME)
        );

        // Reads content of an existing file.
        $existingFile = new File($this->getDataPathPrefix() . self::EXISTING_FILENAME);
        $content = $existingFile->getContent();
        $this->assertNotNull($content);
        $this->assertEquals($originalContent, $content);
    }

    /**
     * Checks the method to get the size of a file.
     */
    public function testFileSize()
    {
        // Gets size of a missing file.
        $hasException = false;
        try {
            $missingFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
            $missingFile->getSize();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates a file with an empty content.
        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $file->create();
        $this->assertEquals(0, $file->getSize());

        // Checks size of an existing file
        $file = new File($this->getDataPathPrefix() . self::EXISTING_FILENAME);
        $content = $file->getContent();
        $this->assertEquals(mb_strlen($content), $file->getSize());
    }

    /**
     * Checks the method to get the real path of a file.
     */
    public function testRealPath()
    {
        // Gets real path of a missing file.
        $hasException = false;
        try {
            $missingFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
            $missingFile->getRealPath();
        } catch (FileNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates a symbolic link, and get its real path.
        $link = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $target = new File(
            $this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1 . File::DIRECTORY_SEPARATOR
                . self::EXISTING_DIRECTORY_2
        );
        $link->createSymbolicLink($target);
        $this->assertEquals(File::normalizePath(realpath($target->getOsPath())), $link->getRealPath());
    }

    /**
     * Checks the method to rename a file.
     */
    public function testRename()
    {
        // Renames a missing file.
        $srcFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
        $destFile = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $hasException = false;
        try {
            $srcFile->rename($destFile);
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException);

        // Renames a file into a missing parent directory.
        $srcFile = new File($this->getDataPathPrefix() . self::EXISTING_FILENAME);
        $destFile = new File(
            $this->getDataPathPrefix() . self::FILE_NAME_1 . File::DIRECTORY_SEPARATOR . self::FILE_NAME_1
        );
        $hasException = false;
        try {
            $srcFile->rename($destFile);
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertTrue($hasException);

        // Renames a file (case change only).
        $srcFile = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        FileUtils::copyFile($this->getDataPathPrefix() . self::EXISTING_FILENAME, $srcFile);
        $destFile = new File(
            $srcFile->getParentPath() . File::DIRECTORY_SEPARATOR . mb_strtoupper($srcFile->getName())
        );
        $srcFile->rename($destFile);
        $this->assertTrue($destFile->exists());
        $destFile->delete();

        // Renames a file.
        FileUtils::copyFile($this->getDataPathPrefix() . self::EXISTING_FILENAME, $srcFile);
        $destFile = new File($this->getDataPathPrefix() . self::FILE_NAME_2);
        $srcFile->rename($destFile);
        $this->assertFalse($srcFile->exists());
        $this->assertTrue($destFile->exists());

        // Renames (and overwrites) a file.
        FileUtils::copyFile($this->getDataPathPrefix() . self::EXISTING_FILENAME, $srcFile);
        $hasException = false;
        try {
            $srcFile->rename($destFile);
        } catch (ExistingFileException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Renames a non-empty directory.
        $srcFile->delete();
        $destFile->delete();
        $sourceDirectory = new File($this->getDataPathPrefix() . self::EXISTING_DIRECTORY_1);
        $sourceDirectory->copy($srcFile);
        $destFile = new File($this->getDataPathPrefix() . self::FILE_NAME_2);
        $srcFile->rename($destFile);
        $this->assertFalse($srcFile->exists());
        $this->assertTrue($destFile->exists());
        $this->assertContentEquals($sourceDirectory, $destFile);

        // Renames a non-empty directory (case change only).
        $destFile->delete(true);
        $srcFile = new File($this->getDataPathPrefix() . self::FILE_NAME_2);
        $sourceDirectory->copy($srcFile);
        $destFile = new File(
            $srcFile->getParentPath() . File::DIRECTORY_SEPARATOR . mb_strtoupper($srcFile->getName())
        );
        $srcFile->rename($destFile);
        $this->assertTrue($destFile->exists());
        $this->assertContentEquals($sourceDirectory, $destFile);
    }

    /**
     * Checks the method to get the date and time of the last modification of a file.
     */
    public function testTouchAndLastModifiedDate()
    {
        // Touch a missing file.
        $hasException = false;
        try {
            $missingFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
            $missingFile->touch();
        } catch (FileNotFoundException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Gets last modified date of a missing file.
        $hasException = false;
        try {
            $missingFile = new File($this->getDataPathPrefix() . self::MISSING_FILENAME);
            $missingFile->getLastModifiedDate();
        } catch (IOException $e) {
            $hasException = true;
        }
        $this->assertException($hasException);

        // Creates a file and gets its last modified date.
        $currentDate = new DateTime();
        $this->assertEquals(0, sleep(1));
        $file = new File($this->getDataPathPrefix() . self::FILE_NAME_1);
        $file->create();
        $dateCreated = $file->getLastModifiedDate();
        $this->assertGreaterThan($currentDate->getTimestamp(), $dateCreated->getTimestamp());

        // Touches the file, and checks its last modified date was updated.
        $this->assertEquals(0, sleep(1));
        $file->touch();
        $this->assertGreaterThan($dateCreated->getTimestamp(), $file->getLastModifiedDate()->getTimestamp());
    }

    /**
     * Asserts the content of two files/directories strictly equal.
     * 
     * @param File $file1 A first file.
     * @param File $file2 A second file.
     */
    private function assertContentEquals(File $file1, File $file2)
    {
        $this->assertTrue($file1->exists());
        $this->assertTrue($file2->exists());
        $this->assertEquals($file1->isFile(), $file2->isFile());
        $this->assertEquals($file1->isDirectory(), $file2->isDirectory());
        $this->assertEquals($file1->isSymbolicLink(), $file2->isSymbolicLink());

        if ($file1->isDirectory()) {
            $fileNames1 = $file1->listFilePaths();
            foreach ($fileNames1 as $fileName1) {
                $innerFile1 = new File($fileName1);
                $innerFile2 = new File(
                    $file2->getPath() . File::DIRECTORY_SEPARATOR . pathinfo($fileName1, PATHINFO_BASENAME));
                $this->assertContentEquals($innerFile1, $innerFile2);
            }

            $fileNames2 = $file2->listFilePaths();
            foreach ($fileNames2 as $fileName2) {
                $this->assertContains(
                    $file1->getPath() . File::DIRECTORY_SEPARATOR . pathinfo($fileName2, PATHINFO_BASENAME),
                    $fileNames1);
            }
        } else {
            $this->assertEquals($file1->getContent(), $file2->getContent());
        }
    }

    /**
     * Deletes a file, and asserts it does not exist anymore.
     * 
     * @param File $file A file.
     * @throws IOException If the file cannot be deleted.
     */
    private function assertDelete(File $file)
    {
        $this->assertTrue($file->exists());
        $this->assertTrue($file->delete());
        $this->assertFalse($file->exists());
    }
}
