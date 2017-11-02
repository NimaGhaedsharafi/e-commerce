<?php

namespace App\Contracts;

/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/2/17
 * Time: 12:07
 */
interface Renderable
{
    /**
     * @return string
     */
    public function render(): string;
}