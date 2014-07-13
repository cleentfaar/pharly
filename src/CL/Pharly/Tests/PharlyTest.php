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
        $destination = __DIR__ . '/tests/test_archive.zip';
        $contents    = [
            __DIR__ . '/tests/test_file.txt',
        ];
        $archive     = $this->pharly->archive($destination, $contents);
        $this->assertInstanceOf('\PharData', $archive);
    }

    public function testExtract()
    {
        $this->markTestIncomplete('Not yet...');
    }
}
