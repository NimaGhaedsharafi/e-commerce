<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/22/17
 * Time: 13:38
 */

namespace AppBundle\Services\Search;


/**
 * Interface SearchService
 * @package AppBundle\Service\Search
 */
interface SearchService
{
    /**
     * @param $keyword
     * @return array
     */
    public function search($keyword);

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    public function add($id, array $data);
}