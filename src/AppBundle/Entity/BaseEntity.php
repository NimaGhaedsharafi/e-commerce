<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/7/17
 * Time: 23:33
 */

namespace AppBundle\Entity;


use AppBundle\Contracts\Collection;

abstract class BaseEntity extends Collection implements \JsonSerializable
{

    /**
     * @param $model
     * @return Collection
     */
    public function hydrate($model)
    {
        // TODO: Implement hydrate() method.
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}