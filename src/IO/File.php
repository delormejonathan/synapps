<?php

namespace Inneair\Synapps\IO;

use DateTime;
use Inneair\Synapps\System\OS;
use Normalizer;

/**
 * This class encapsulates properties and methods available to read/write content from/into a file. This is a portable
 * version of the PHP file functions, providing high level object oriented interface, and hiding operating systems
 * specific requirements.
 * This interface relies on file system's case sensitivity support, which means, for instance, two directories with the
 * same name in a different case can be created if the underlying file system supports case-sensitivity.
 * FIXME: check all methods to ensure this contract is well implemented.
 */
class File
{
    /**
     * Portable directory separator.
     * @var string
     */
    const DIRECTORY_SEPARATOR = '/';
    /**
     * Windows file system encoding (CP1252).
     * @var string
     */
    const WINDOWS_FS_ENCODING = 'Windows-1252';
    /**
     * Default mode when creating a file/directory.
     * @var int
     */
    const DEFAULT_MODE = 0777;

    /**
     * Path of this file.
     * @var string
     */
    private $path;
    /**
     * OS-encoded path. For Windows, this is the CP1252 encoded path. For other platforms, it is the same value than the
     * <code>path</code> property.
     * @var string
     */
    private $osPath;

    /**
     * Builds an abstract file mapped to the given path.
     *
     * The given path is automatically normalized, using the <code>normalize</code> method.
     *
     * @param string $path File path.
     */
    public function __construct($path)
    {
        $this->path = self::normalizePath($path);
        $this->osPath = self::encodeOsFileName($this->path);
    }

    /**
     * Copies a file or a directory recursively to a destination file/directory.
     *
     * When copying, the parent directory must already exist, or an exception will be thrown. If a symbolic link targets
     * a file, it is copied as it is. If it targets a directory, the symbolic link is copied as a regular directory in
     * the destination, and the content in the source target is copied recursively.
     * WARNING: this method may overwrite existing files in the destination directory (if they have both the same type,
     * for instance regular files). It is up to the caller to check if the destination directory is empty or not.
     *
     * @param File|string $destination Abstract file for the destination path, or the destination path itself.
     * @throws FileNotFoundException If the parent directory does not exist.
     * @throws ExistingFileException If a file/directory/symbolic link already exists with this name (case-insensitive).
     * @throws IOException If a directory cannot be created in the destination path, or if a file cannot be copied.
     */
    public function copy($destination)
    {
        $destinationFile = ($destination instanceof self) ? $destination : new self($destination);
        $parentFile = new self($destinationFile->getParentPath());
        if (!$parentFile->exists()) {
            throw new FileNotFoundException($parentFile->getPath());
        }
        if (mb_strtolower($this->getPath()) === mb_strtolower($destinationFile->getPath())) {
            throw new ExistingFileException($destinationFile->getPath());
        }

        if ($this->isDirectory()) {
            // Creates the destination directory.
            $destinationFile->createDirectory();

            // Copies directory content.
            $directory = dir($this->osPath);
            $innerOsFilename = $directory->read();
            try {
                while ($innerOsFilename !== false) {
                    if (($innerOsFilename !== '.') && ($innerOsFilename !== '..')) {
                        $innerFilename = self::decodeOsFileName($innerOsFilename);
                        $innerFile = new self($this->path . self::DIRECTORY_SEPARATOR . $innerFilename);
                        $innerFile->copy($destinationFile->getPath() . self::DIRECTORY_SEPARATOR . $innerFilename);
                    }

                    $innerOsFilename = $directory->read();
                }
                $directory->close();
            } catch (IOException $e) {
                $directory->close();
                throw $e;
            }
        } elseif (!@copy($this->osPath, $destinationFile->getOsPath())) {
            throw new IOException(
                'Cannot copy file from \'' . $this->path . '\' to \'' . $destinationFile->getPath() . '\''
            );
        }

        // Clears PHP cache so as it gives updated information about the destination file.
        clearstatcache(true, $destinationFile->getOsPath());
    }

    /**
     * Creates an empty file.
     *
     * @throws ExistingFileException If a file/directory/symbolic link already exists with this name (case-insensitive).
     * @throws IOException If the file cannot be created.
     */
    public function create()
    {
        if ($this->exists()) {
            throw new ExistingFileException($this->path);
        }

        if (@touch($this->osPath) === false) {
            throw new IOException('Cannot create file \'' . $this->path . '\'');
        }
    }

    /**
     * Creates a directory denoted by this file (optionally, intermediate directories may be created also if missing).
     *
     * @param int $mode The mode is <code>DEFAULT_MODE</code> by default, which means the widest possible access. For
     * more information on modes, read the details on the chmod() page. It is not supported on Windows platforms.
     * @param bool $recursive If intermediate directories must be created if missing (defaults to <code>false</code>).
     * @return bool <code>true</code> if the directory was created, <code>false</code> if it already exists.
     * @throws ExistingFileException If a file/symbolic link already exists with this name (case-insensitive).
     * @throws IOException If the directory cannot be created.
     */
    public function createDirectory($mode = self::DEFAULT_MODE, $recursive = false)
    {
        if ($this->isDirectory()) {
            return false;
        } elseif ($this->exists()) {
            throw new ExistingFileException($this->path);
        }

        $result = @mkdir($this->osPath, $mode, $recursive);
        if (!$result) {
            throw new IOException('Cannot create directory \'' . $this->path . '\'');
        }

        return true;
    }

    /**
     * Creates a symbolic link from this file to the target path.
     *
     * @param File|string $target Abstract file for the target path, ot the target path itself of the symbolic link.
     * @throws FileNotFoundException If the target file does not exist.
     * @throws ExistingFileException If a file/directory/symbolic link already exists with this name (case-insensitive).
     * @throws IOException If this link can not be created.
     */
    public function createSymbolicLink($target)
    {
        $targetFile = ($target instanceof self) ? $target : new self($target);
        if (!$targetFile->exists()) {
            throw new FileNotFoundException($targetFile->getPath());
        }

        if ($this->exists()) {
            throw new ExistingFileException($this->path);
        }

        if (!@symlink($targetFile->getOsPath(), $this->osPath)) {
            throw new IOException(
                'Cannot create symbolic link \'' . $this->path . '\' to \'' . $targetFile->getPath() . '\''
            );
        }
    }

    /**
     * Decodes a file name given by the OS, so as it can be used normally in the application.
     *
     * Under Windows, this method will decode special using the CP1252 encoding.
     *
     * @param string $fileName OS file name.
     * @return string An UTF-8 file name.
     */
    public static function decodeOsFileName($fileName)
    {
        $os = OS::getInstance();
        if ($os->isWindows()) {
            return mb_convert_encoding($fileName, 'UTF-8', self::WINDOWS_FS_ENCODING);
        } elseif ($os->isMacintosh()) {
            return Normalizer::normalize($fileName, Normalizer::FORM_C);
        } else {
            return $fileName;
        }
    }

    /**
     * Deletes this file in the file system.
     *
     * For security reasons, symbolic links are never followed when deleting, and there target is therefore preserved.
     * Targets must be deleted with their real path. Additionally, this method clears PHP cache for this file.
     *
     * @param bool $recursive In case of a directory that is not a symbolic link, all its content will be deleted.
     * If <code>false</code> and the directory is not empty, then an exception will be thrown (defaults to
     * <code>false</code>). This parameter is not relevant for regular files or symbolic links.
     * @return bool <code>true</code> if this file was deleted, <code>false</code> if it does not exist.
     * @throws IOException If the file cannot be deleted.
     */
    public function delete($recursive = false)
    {
        if (!$this->exists()) {
            return false;
        }

        if ($this->isDirectory()) {
            if ($recursive && !$this->isSymbolicLink()) {
                $directory = dir($this->osPath);
                $innerOsFilename = $directory->read();
                try {
                    while ($innerOsFilename !== false) {
                        if (($innerOsFilename !== '.') && ($innerOsFilename !== '..')) {
                            $innerFile = new self(
                                $this->path . self::DIRECTORY_SEPARATOR . self::decodeOsFileName($innerOsFilename)
                            );
                            $innerFile->delete($recursive);
                        }

                        $innerOsFilename = $directory->read();
                    }
                    $directory->close();
                } catch (IOException $e) {
                    $directory->close();
                    throw $e;
                }
            }

            $result = $this->deleteDirectory();
        } else {
            $result = $this->deleteFile();
        }

        if ($result === false) {
            throw new IOException('Cannot delete file \'' . $this->path . '\'');
        }

        // Clears PHP cache so as it gives updated information about this file.
        clearstatcache(true, $this->osPath);

        return true;
    }

    /**
     * Internal method useful to delete an empty directory.
     *
     * The file may be a directory. On Windows, this method must be used to deleted symbolic links to directories,
     * because they are considered as directories.
     *
     * @return boolean <code>true</code> if the directory was deleted, <code>false</code> otherwise.
     */
    private function deleteDirectory()
    {
        // If the directory is not a symbolic link => deletes it with 'rmdir'.
        // If the directory is a symbolic link on a non-Windows OS => deletes it with 'unlink'.
        // If the directory is a symbolic link on a Windows OS => deletes it with 'rmdir'.
        if (!$this->isSymbolicLink() || OS::getInstance()->isWindows()) {
            // On Windows, symbolic links whose target is a directory must be deleted with 'rmdir'.
            $result = @rmdir($this->osPath);
        } else {
            $result = @unlink($this->osPath);
        }
        return $result;
    }

    /**
     * Internal method useful to delete a file.
     *
     * The file may be a regular file, a symbolic link. On Windows systems, the link must have been created to a file.
     *
     * @return boolean <code>true</code> if the directory was deleted, <code>false</code> otherwise.
     */
    private function deleteFile()
    {
        $result = @unlink($this->osPath);
        if (($result === false) && OS::getInstance()->isWindows()) {
            // On Windows, unresolved symbolic links may be deleted either with 'unlink' or 'rmdir'.
            // This behaviour depends on the target type of the link:
            // - If the link was created to a directory (that may not exist anymore), then 'rmdir' must be used.
            // - If the link was created to a file (that may not exist anymore), then 'unlink' must be used.
            $result = @rmdir($this->osPath);
        }
        return $result;
    }

    /**
     * Encodes a file name so as it is OS-compliant.
     *
     * Under Windows, this method will convert special characters in the file name into the CP1252 encoding. The file
     * name must not be CP1252 encoded before calling this method, or it will be lead to an unexpected behaviour.
     *
     * @param string $fileName File name.
     * @return string The OS-ready file name, which may be unmodified under non-Windows OS.
     */
    public static function encodeOsFileName($fileName)
    {
        $os = OS::getInstance();
        if ($os->isWindows()) {
            return mb_convert_encoding($fileName, self::WINDOWS_FS_ENCODING);
        } elseif ($os->isMacintosh()) {
            return Normalizer::normalize($fileName, Normalizer::FORM_D);
        } else {
            return $fileName;
        }
    }

    /**
     * Tells if this file exists in the file system.
     *
     * This method supports symbolic links whose target does not exist (i.e. returns <code>true</code>). To check
     * existence of a symbolic link's target, the real path.
     *
     * @return bool <code>true</code> if this file exists, <code>false</code> otherwise.
     */
    public function exists()
    {
        // PHP 'file_exists' function does not support unresolved symbolic links, so we do it on our own.
        return (@file_exists($this->osPath) || $this->isSymbolicLink());
    }

    /**
     * Reads and returns the content of this file.
     *
     * @throws IOException If the file cannot be opened, or the content be read.
     * @return string File content.
     */
    public function getContent()
    {
        $content = @file_get_contents($this->osPath);
        if ($content === false) {
            throw new IOException('Cannot read content from file \'' . $this->path . '\'');
        }

        return $content;
    }

    /**
     * Gets the extension of this file.
     *
     * @return string Extension of this file.
     */
    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Gets the last modified date of this file.
     *
     * This method clears the PHP stat cache to ensure updated date and time are returned, and not cached values.
     *
     * @return DateTime Date and time of the last modification.
     * @throws IOException If the date and time cannot be read.
     */
    public function getLastModifiedDate()
    {
        $date = @filemtime($this->osPath);
        if ($date === false) {
            throw new IOException('Cannot read last modified date of file \'' . $this->path . '\'');
        }

        return new DateTime('@' . $date);
    }

    /**
     * Gets the OS-compliant path of this file.
     *
     * The path never contains a trailing slash '/' or backslash '\' character.
     *
     * @return string File path.
     */
    public function getOsPath()
    {
        return $this->osPath;
    }

    /**
     * Gets the name of this file (without the parent path).
     *
     * @return string File name.
     */
    public function getName()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    /**
     * Gets the parent path of this file.
     *
     * @return string Parent path.
     */
    public function getParentPath()
    {
        return pathinfo($this->getPath(), PATHINFO_DIRNAME);
    }

    /**
     * Gets the path of this file.
     *
     * The path never contains a trailing slash '/' or backslash '\' character.
     *
     * @return string File path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Gets the real path (normalized) of this file, replacing symbolic links, '.' and '..' directories with the real
     * absolute path and portable directory separator.
     *
     * @return string The real file path.
     * @throws FileNotFoundException If this file does not exist (in case of a symbolic link, the target may not exist).
     */
    public function getRealPath()
    {
        $realPath = realpath($this->osPath);
        if ($realPath === false) {
            throw new FileNotFoundException($this->path);
        }

        return self::normalizePath($realPath);
    }

    /**
     * Gets the size of this file.
     *
     * @return int|string Size (bytes) of this file. The result may be a string if the size is greater than the maximum
     * int value supported by PHP.
     * @throws IOException If this file is not a regular file, or is not readable.
     */
    public function getSize()
    {
        $size = @filesize($this->osPath);
        if ($size === false) {
            throw new IOException('Cannot read size of file \'' . $this->path . '\'');
        }

        return $size;
    }

    /**
     * Tells whether this file is a directory.
     *
     * @return bool <code>true</code> if this file is a directory, <code>false</code> otherwise.
     */
    public function isDirectory()
    {
        return @is_dir($this->osPath);
    }

    /**
     * Tells whether this file is a regular file.
     *
     * @return bool <code>true</code> if this file is a regular file, <code>false</code> otherwise.
     */
    public function isFile()
    {
        return @is_file($this->osPath);
    }

    /**
     * Tells whether this file is a link.
     *
     * @return bool <code>true</code> if this file is a link, <code>false</code> otherwise.
     */
    public function isSymbolicLink()
    {
        return @is_link($this->osPath);
    }

    /**
     * Tells whether this file/directory is writable.
     *
     * @return bool <code>true</code> if this file/directory is writable.
     */
    public function isWritable()
    {
        return @is_writable($this->osPath);
    }

    /**
     * Lists all files within this directory.
     *
     * Special directories '.' and '..' are not listed.
     *
     * @param string $pattern A PCRE pattern to filter file names, applied on basename (defaults to <code>null</code>,
     * which means all files are listed). The pattern must be compliant with the PHP 'mb_ereg_match' function, i.e.
     * must match whole file paths. The <code>RegexUtils::quote</code> method may be used to escape special characters
     * in the pattern.
     * @return string[] Array of absolute paths.
     * @throws IOException If this file is not a directory, or its content cannot be listed.
     */
    public function listFilePaths($pattern = null)
    {
        if (!$this->isDirectory()) {
            throw new IOException('\'' . $this->path . '\' is not a valid directory');
        }

        $directory = dir($this->osPath);
        $paths = array();
        try {
            $innerOsFilename = $directory->read();
            $useFilter = ($pattern !== null);
            while ($innerOsFilename !== false) {
                $innerFilename = self::decodeOsFileName($innerOsFilename);
                if (($innerOsFilename !== '.') && ($innerOsFilename !== '..')) {
                    $result = ($useFilter) ? mb_ereg_match($pattern, $innerFilename) : true;
                    if ($result) {
                        $paths[] = $this->path . self::DIRECTORY_SEPARATOR . $innerFilename;
                    }
                }

                $innerOsFilename = $directory->read();
            }
            $directory->close();
        } catch (IOException $e) {
            $directory->close();
            throw $e;
        }

        return $paths;
    }

    /**
     * Normalizes a path.
     *
     * The normalized path contains only one kind of directory separator, and trailing separators are removed. This
     * method supports multi-byte encoded paths.
     *
     * @param string $path Path to be normalized.
     * @param bool $normalizeSeparators If directory separators must be normalized (defaults to <code>true</code>).
     * @param bool $usePortableDirectorySeparator If a portable directory separator must be used, or the system' one
     * (defaults to <code>true</code>). This parameter is relevant only if <code>$normalizeSeparators</code> is
     * <code>true</code>.
     * @return string The normalized path.
     */
    public static function normalizePath($path, $normalizeSeparators = true, $usePortableDirectorySeparator = true)
    {
        $path = rtrim($path, '/\\');
        if ($normalizeSeparators) {
            $searchedDirectorySeparator =
                ($usePortableDirectorySeparator ? DIRECTORY_SEPARATOR : self::DIRECTORY_SEPARATOR);
            $newDirectorySeparator = ($usePortableDirectorySeparator) ? self::DIRECTORY_SEPARATOR : DIRECTORY_SEPARATOR;
            $pattern = str_replace('\\', '\\\\', $searchedDirectorySeparator);
            $result = mb_ereg_replace($pattern, $newDirectorySeparator, $path);
            if ($result !== false) {
                $path = $result;
            }
        }
        return $path;
    }

    /**
     * Reads a file and writes it to the output buffer.
     *
     * @return int|string Number of bytes read from this file.
     * @throws IOException If this file cannot be read.
     */
    public function read()
    {
        $numberOfBytes = @readfile($this->osPath);
        if ($numberOfBytes === false) {
            throw new IOException('Cannot read file \'' . $this->path . '\'');
        }

        return $numberOfBytes;
    }

    /**
     * Renames this file into another file (move operation).
     *
     * Additionally, this method clears PHP cache for this file, and the destination file.
     *
     * @param File|string $destination Abstract file for the destination path, or the destination path itself.
     * @throws ExistingFileException If the destination file already exists.
     * @throws IOException If the file cannot be renamed.
     */
    public function rename($destination)
    {
        $destinationFile = ($destination instanceof self) ? $destination : new self($destination);
        // Renaming with a case change is enabled, otherwise the destination file must not already exist.
        if ((mb_strtolower($this->getPath()) !== mb_strtolower($destinationFile->getPath()))
            && $destinationFile->exists()
        ) {
            throw new ExistingFileException($destinationFile->getPath());
        }

        $result = @rename($this->osPath, $destinationFile->getOsPath());
        if ($result === false) {
            throw new IOException(
                'Cannot rename file \'' . $this->path . '\' into \'' . $destinationFile->getPath() . '\''
            );
        }

        // Clears PHP cache so as it gives updated information about this file, and the destination file.
        clearstatcache(true, $this->osPath);
        clearstatcache(true, $destinationFile->getOsPath());
    }

    /**
     * Writes the content of this file (defaults to overwrite, see <code>flags</code> parameter for other modes).
     *
     * @param mixed $content Content.
     * @param int $flags I/O flags (see PHP <code>file_put_contents</code> function, defaults to 0).
     * @throws IOException If the file cannot be opened, or the content written.
     * @return int Number of bytes written in this file.
     */
    public function setContent($content, $flags = 0)
    {
        $numberOfBytes = @file_put_contents($this->osPath, $content, $flags);
        if ($numberOfBytes === false) {
            throw new IOException('Cannot write content to file \'' . $this->path . '\'');
        }

        return $numberOfBytes;
    }

    /**
     * Sets this file's permissions.
     *
     * This method is not supported under Windows OS, and does nothing.
     *
     * @param int $mode The new mode (must start with a leading 0 if using octal notation).
     * @throws IOException If the file/directory does not exist, or permissions cannot be set.
     */
    public function setPermissions($mode)
    {
        $result = chmod($this->osPath, $mode);
        if (!$result) {
            throw new IOException('Cannot set permissions ' . $mode . ' for file \'' . $this->path . '\'');
        }
    }

    /**
     * Sets the dates and times of access and modification.
     *
     * NOTE: this implementation supports more consistently missing files, as it throws an exception in this case,
     * contrary to the PHP 'touch' function, which creates the file. Additionally, this method clears PHP cache for this
     * file.
     *
     * @throws FileNotFoundException If the file does not exist.
     * @throws IOException If the dates and times cannot be set.
     */
    public function touch()
    {
        if (!$this->exists()) {
            throw new FileNotFoundException($this->path);
        }

        $result = touch($this->osPath);
        if (!$result) {
            throw new IOException(
                'Cannot set date and time of access and modification for file \'' . $this->path . '\''
            );
        }

        // Clears PHP cache so as it gives updated information about this file.
        clearstatcache(true, $this->osPath);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return 'File {path=' . $this->path . ', osPath=' . $this->osPath . '}';
    }
}
