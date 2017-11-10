<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/10/17
 * Time: 14:15
 */

namespace AppBundle\Exception;


class NotFoundEntity extends BaseException
{
    /**
     * NotFoundEntity constructor.
     */
    public function __construct()
    {
        parent::__construct(404, 'Entity not found');
    }
}