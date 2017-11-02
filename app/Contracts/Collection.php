<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/2/17
 * Time: 12:09
 */

namespace App\Contracts;


/**
 * Class Collection
 * @package App\Contracts
 */
abstract class Collection implements Arrayable, Renderable
{
    /**
     * @param $model
     * @return Collection
     */
    abstract public function hydrate($model);

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    abstract public function toArray(): array;
}