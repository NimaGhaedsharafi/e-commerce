<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/10/17
 * Time: 14:15
 */

namespace AppBundle\Exception;


use Symfony\Component\HttpFoundation\Response;

/**
 * Class ValidationFailed
 * @package AppBundle\Exception
 */
class ValidationFailed extends BaseException
{
    /**
     * NotFoundEntity constructor.
     */
    public function __construct()
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Failed');
    }
}