<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/7/17
 * Time: 20:03
 */

namespace Tests\AppBundle\Controller;


use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends ApiTest
{
    /**
     * @test
     */
    public function index_should_return_all_of_the_products()
    {
        $client = $this->createClient();
        $client->request('GET', 'products');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->seeJsonStructure([
            '*' => [
                'title',
                'description',
            ]
        ], $this->getDecodedResponse($client));
    }

    /**
     * @test
     */
    public function create_product_should_return_the_product()
    {
        $client = $this->createClient();
        $client->request('POST', 'products/create', ['title' => 'random-name', 'description' => 'some-random-desc']);

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $this->seeJsonStructure([
            'title',
            'description',
        ], $this->getDecodedResponse($client));
    }
}