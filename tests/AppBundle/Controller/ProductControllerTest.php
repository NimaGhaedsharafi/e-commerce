<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/7/17
 * Time: 20:03
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends ApiTest
{
    /**
     * @test
     */
    public function index_should_return_all_of_the_products()
    {
        $this->client->request('GET', 'products');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->seeJsonStructure([
            '*' => [
                'title',
                'description',
            ]
        ], $this->getDecodedResponse($this->client));
    }

    /**
     * @test
     */
    public function create_product_should_return_the_product()
    {
        $count = $this->doctrine->getRepository(Product::class)->count();

        $this->client->request('POST', 'products/create', ['title' => 'random-name', 'description' => 'some-random-desc']);

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->seeJsonStructure([
            'title',
            'description',
        ], $this->getDecodedResponse($this->client));

        $this->assertEquals($count + 1, $this->doctrine->getRepository(Product::class)->count());
    }
}