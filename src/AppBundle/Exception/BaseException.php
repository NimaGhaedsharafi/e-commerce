<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/10/17
 * Time: 14:15
 */

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseException extends HttpException
{
    public function __construct($statusCode = 400, $message = '', $headers = [])
    {
        parent::__construct($statusCode, $message, null, $headers, 0);
    }
}