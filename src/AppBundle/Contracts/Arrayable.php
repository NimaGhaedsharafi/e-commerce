<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/2/17
 * Time: 12:11
 */

namespace AppBundle\Contracts;


/**
 * Interface Arrayable
 * @package App\Contracts
 */
interface Arrayable
{
    /**
     * @return array
     */
    public function toArray() : array;
}