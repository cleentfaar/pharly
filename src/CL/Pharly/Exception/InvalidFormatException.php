<?php

/*
 * This file is part of Pharly
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\Pharly\Exception;

class InvalidFormatException extends \Exception
{
    /**
     * @param string     $invalidFormat The format deemed invalid
     * @param int        $code          [optional] The Exception code.
     * @param \Exception $previous      [optional] The previous exception used for the exception chaining.
     */
    public function __construct($invalidFormat, $code, \Exception $previous = null)
    {
        $message = sprintf('The given format "%s" is not supported, use either "phar", "zip" or "tar"', $invalidFormat);

        parent::__construct($message, $code, $previous);
    }
}
