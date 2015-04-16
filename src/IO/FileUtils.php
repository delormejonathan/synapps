<?php

namespace Inneair\Synapps\IO;

/**
 * Class providing general files and directories related utilities.
 */
class FileUtils
{
    /**
     * Prevents unwanted instantiations.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Copies a file or a directory recursively to a destination file or directory.
     *
     * This method is a shortcut for the <code>File::rename</code> method.
     *
     * @param string $sourcePath Source file or directory.
     * @param File|string $destination Abstract file for the destination path, or the destination path itself.
     * @throws FileNotFoundException If the parent directory does not exist.
     * @throws IOException If a directory cannot be created in the destination path, or if a file cannot be copied.
     */
    public static function copyFile($sourcePath, $destination)
    {
        $sourceFile = new File($sourcePath);
        $sourceFile->copy($destination);
    }

    /**
     * Deletes a file/directory/symbolic link recursively.
     *
     * This method is a shortcut for the <code>File::delete</code> method. For security reasons, symbolic links' target
     * are never deleted.
     *
     * @param string $fileName File or directory name.
     * @param bool $recursive In case of a directory that is not a symbolic link, all its content will be deleted.
     * If <code>false</code> and the directory is not empty, then an exception will be thrown (defaults to
     * <code>false</code>). This parameter is not relevant for regular files or symbolic links.
     * @return bool <code>true</code> if this file was deleted, <code>false</code> if it does not exist.
     * @throws IOException If the file cannot be deleted.
     */
    public static function deleteFile($fileName, $recursive = false)
    {
        $file = new File($fileName);
        return $file->delete($recursive);
    }

    /**
     * Renames (or moves) a file or a directory.
     *
     * This method is a shortcut for the <code>File::rename</code> method.
     *
     * @param string $sourcePath Path to the file that must be renamed.
     * @param File|string $destination Abstract file for the destination path, or the destination path itself.
     * @throws IOException If the file/directory cannot be renamed, or the destination file already exists.
     */
    public static function renameFile($sourcePath, $destination)
    {
        $sourceFile = new File($sourcePath);
        $sourceFile->rename($destination);
    }
}
