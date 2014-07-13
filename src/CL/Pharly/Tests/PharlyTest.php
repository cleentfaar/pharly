<?php

/*
 * This file is part of Pharly
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Pharly\Tests;

use CL\Pharly\Pharly;

class PharlyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pharly
     */
    protected $pharly;

    /**
     * @var Pharly|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pharlyMock;

    protected function setUp()
    {
        $this->pharly = new Pharly();
    }

    /**
     * Tests the 'archive()' method for each of the available formats/extensions
     */
    public function testArchive()
    {
        $this->createAndTestArchive('.zip');
        $this->createAndTestArchive('.tar');
        $this->createAndTestArchive('.tar.gz');
        $this->createAndTestArchive('.tar.bz2');
    }

    /**
     * Tests the 'extract()' method for each of the available formats/extensions
     */
    public function testExtract()
    {
        $this->createAndTestArchiveExtraction('.zip');
        $this->createAndTestArchiveExtraction('.tar');
        $this->createAndTestArchiveExtraction('.tar.gz');
        $this->createAndTestArchiveExtraction('.tar.bz2');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFormat()
    {
        $this->createArchive('.abc');
    }

    /**
     * @expectedException \LogicException
     */
    public function testOverwriteLogic()
    {
        // create the initial archive
        $this->createArchive('zip');

        // create another archive with same destination,
        // overwriting the existing archive should be prevented if $allowOverwrite is false
        $this->createArchive('zip', false);
    }

    /**
     * @expectedException \CL\Pharly\Exception\ArchivalException
     */
    public function testArchiveNonExistingFiles()
    {
        $this->createArchive('.zip', false, ['path/to/nonexistingfile']);
    }

    /**
     * @expectedException \CL\Pharly\Exception\ExtractionException
     */
    public function testExtractNonExistingFiles()
    {
        $destination = __DIR__ . '/test_archive/test_archive.zip';
        $this->pharly->archive($destination, [], \Phar::ZIP);
        $this->pharly->extract($destination, __DIR__ . '/test_extract/testdir', ['non/existing/file.txt']);
        unlink($destination);
    }

    /**
     * Creates an archive and asserts whether it has the correct content.
     *
     * @param string $extension The file extension to determine what kind of archive to create.
     */
    protected function createAndTestArchive($extension)
    {
        $destination = $this->getPathToArchive($extension);
        $archive     = $this->createArchive($extension);
        $this->assertInstanceOf('\PharData', $archive);
        $this->assertFileExists($destination);
        $this->assertContains('Hello World!', $archive['my/file.txt']->getContent());
        unlink($destination);
    }

    /**
     * Creates a testing archive and asserts whether it can be extracted successfully.
     *
     * @param string $extension The file extension to determine what kind of archive to create.
     */
    protected function createAndTestArchiveExtraction($extension)
    {
        $destination = $this->getPathToArchive($extension);
        $this->createArchive($extension);
        $this->pharly->extract($destination, $this->getPathToExtractionDir());
        $extractedFilePath = $this->getPathToExtractionDir() . 'my/file.txt';
        $this->assertFileExists($extractedFilePath);
        $this->assertContains('Hello World!', file_get_contents($extractedFilePath));
        $this->rmdirRecursive(dirname($extractedFilePath));
        unlink($destination);
    }

    /**
     * Recursively removes a directory that may have been created during extraction.
     *
     * @param string $path Path to the directory.
     *
     * @return bool When removal was successful.
     */
    protected function rmdirRecursive($path)
    {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            $pathFile = sprintf('%s/%s', $path, $file);
            if (is_dir($pathFile)) {
                $this->rmdirRecursive($pathFile);
            } else {
                unlink($pathFile);
            }
        }

        return rmdir($path);
    }

    /**
     * Returns a testing location used to extract archives.
     *
     * @return string The resulting path.
     */
    protected function getPathToExtractionDir()
    {
        return __DIR__ . '/test_extract/';
    }

    /**
     * Creates an archive from a given extension, to be used during tests.
     *
     * @param string $extension      The extension to create the archive with.
     * @param bool   $allowOverwrite Whether overwriting of existing archives should be allowed.
     * @param array  $contents       The contents of the archive
     *
     * @return \PharData The created archive.
     */
    protected function createArchive($extension, $allowOverwrite = false, array $contents = [])
    {
        $destination = $this->getPathToArchive($extension);
        if (file_exists($destination)) {
            unlink($destination);
        }
        if (empty($contents)) {
            $contents = [
                'my/file.txt' => $this->getPathToTestFile(),
            ];
        }
        $archive = $this->pharly->archive($destination, $contents, null, $allowOverwrite);

        return $archive;
    }

    /**
     * @param string $extension The extension to create the path with.
     *
     * @return string The resulting path.
     */
    protected function getPathToArchive($extension)
    {
        return sprintf(__DIR__ . '/test_archive/test_archive%s', $extension);
    }

    /**
     * @return string The path to a testing file that can be used to create a new archive with.
     */
    protected function getPathToTestFile()
    {
        return __DIR__ . '/test_archive/test_file.txt';
    }
}
