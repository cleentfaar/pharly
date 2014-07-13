<?php

/*
 * This file is part of Pharly
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Pharly;

use CL\Pharly\Exception\ArchivalException;
use CL\Pharly\Exception\ExtractionException;

class Pharly
{
    /**
     * @param string|null       $destination    The destination of the archive.
     * @param string|array|null $contents       The file(s) or director(y/ies) to archive.
     *                                          Use an array for multiple files/directories.
     *                                          Leave as null to only create the archive.
     * @param string|null       $format         The format used for archiving, leave as null to determine
     *                                          it from the destination's extension.
     * @param bool              $allowOverwrite Whether overwriting an existing archive should be allowed.
     * @param bool              $deleteOriginal Whether the original files should be deleted when
     *                                          archiving has been successful.
     *
     * @return \PharData The created archive.
     *
     * @throws \LogicException If the given destination already exists and $allowOverwrite is false.
     */
    public function archive(
        $destination,
        array $contents = [],
        $format = null,
        $allowOverwrite = false,
        $deleteOriginal = false
    )
    {
        if ($format === null) {
            $format = $this->determineFormat($destination);
        } else {
            $destination = $this->ensureExtension($destination, $format);
        }
        var_dump($destination);
        if ($allowOverwrite === false && file_exists($destination)) {
            throw new \LogicException(sprintf(
                'Couldn\'t create archive with destination: "%s"; it already exists and $allowOverwrite is FALSE',
                $destination
            ));
        }
        $archive = $this->createArchive($format, $destination, $contents);
        if ($deleteOriginal === true) {
            foreach ($contents as $path) {
                unlink($path);
            }
        }

        return $archive;
    }

    /**
     * @param string            $path           The path to the archive.
     * @param string            $destination    The directory to extract any contents into.
     * @param null|array|string $files          Any specific file(s) to extract as a string or array,
     *                                          defaults to NULL which extracts everything.
     * @param bool              $allowOverwrite Whether overwrite of any existing files should be allowed
     *                                          during extraction.
     * @param bool              $deleteArchive  Whether the archive should be deleted after successful extraction
     *
     * @throws ExtractionException If extraction failed.
     */
    public function extract($path, $destination, $files = null, $allowOverwrite = false, $deleteArchive = false)
    {
        try {
            $phar = new \PharData($path);
            $phar->extractTo($destination, $files, $allowOverwrite);
        } catch (\Exception $e) {
            throw new ExtractionException(
                sprintf('Failed to extract contents from archive with path: "%s"', $destination), null, $e
            );
        }

        if ($deleteArchive === true) {
            unlink($path);
        }
    }

    /**
     * @param string $path      The path to ensure it has the given extension.
     * @param string $extension The extension that the given path must end with.
     *
     * @return string
     */
    protected function ensureExtension($path, $extension)
    {
        if (substr($path, -(strlen($extension))) !== $extension) {
            return $path . $extension;
        }

        return $path;
    }

    /**
     * @param string $path The path to extract the format from.
     *
     * @return int The PHAR-format extracted from the given path.
     *
     * @throws \InvalidArgumentException If none of the supported formats could be determined from the given path.
     */
    protected function determineFormat($path)
    {
        $extension = $this->extractExtension($path);

        switch ($extension) {
            case '.zip':
                return \Phar::ZIP;
            case '.tar':
                return \Phar::TAR;
            case '.tar.gz':
                return \Phar::GZ;
            case '.tar.bz2':
                return \Phar::BZ2;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'There is no algorithm that supports that extension: "%s" (found in path: %s)',
                    $extension,
                    $path
                ));
        }
    }

    /**
     * @param string $format      The algorithm used for the archive.
     * @param string $destination The path to the new archive.
     * @param array  $contents    The files and/or directories that should be included in the archive.
     *
     * @return \PharData
     *
     * @throws ArchivalException If the archive could not be created.
     */
    protected function createArchive($format, $destination, array $contents = [])
    {
        try {
            $archive = new \PharData($destination, null, null, $format);
            foreach ($contents as $pathInArchive => $path) {
                if (is_integer($pathInArchive)) {
                    $pathInArchive = null;
                }
                if (!is_readable($path)) {
                    throw new ArchivalException(sprintf(
                        'Could not add the given file/dir to the archive: %s, it doesn\'t exist',
                        $path
                    ));
                }
                if (is_dir($path)) {
                    $archive->buildFromDirectory($path);
                } else {
                    if ($pathInArchive !== null) {
                        $archive->addFile($path, $pathInArchive);
                    } else {
                        $archive->addFile($path);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new ArchivalException(
                sprintf('Failed to create archive with destination: %s', $destination),
                null,
                $e
            );
        }

        return $archive;
    }

    /**
     * @param string $path Path to determine the file extension from.
     *
     * @return string|null The extension or null if no extension could be extracted from the given path.
     */
    protected function extractExtension($path)
    {

        $parts     = pathinfo($path);
        $extension = array_key_exists('extension', $parts) ? '.' . $parts['extension'] : null;
        if ($extension == '.gz' || $extension == '.bz2') {
            $earlierExtension = pathinfo($parts['filename'], PATHINFO_EXTENSION);
            $extension        = ($earlierExtension ? '.' . $earlierExtension : '') . $extension;
        }

        if (!empty($extension)) {
            return $extension;
        }

        return null;
    }
}
