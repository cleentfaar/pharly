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

    public function testArchive()
    {
        $this->createAndTestArchive('zip');
        $this->createAndTestArchive('tar');
        $this->createAndTestArchive('tar.gz');
        $this->createAndTestArchive('tar.bz2');
    }

    public function testExtract()
    {
        $this->markTestIncomplete('Not yet...');
    }

    /**
     * @param string $extension
     */
    protected function createAndTestArchive($extension)
    {
        $destination = $this->getPathToArchive($extension);
        if (file_exists($destination)) {
            unlink($destination);
        }
        $archive     = $this->pharly->archive($destination, [
            'my/file.txt' => $this->getPathToTestFile(),
        ]);
        $this->assertInstanceOf('\PharData', $archive);
        $this->assertTrue(file_exists($destination));
        $this->assertContains('Hello World!', $archive['my/file.txt']->getContent());
        unlink($destination);
    }

    /**
     * @param string $extension
     *
     * @return string
     */
    protected function getPathToArchive($extension)
    {
        return sprintf(__DIR__ . '/test_archive/test_archive.%s', $extension);
    }

    /**
     * @return string
     */
    protected function getPathToTestFile()
    {
        return __DIR__ . '/test_archive/test_file.txt';
    }
}
