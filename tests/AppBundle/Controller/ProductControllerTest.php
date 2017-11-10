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

    /**
     * @test
     */
    public function delete_a_valid_product_should_work()
    {
        $count = $this->doctrine->getRepository(Product::class)->count();

        // there's no factory for entity so let's send a request to create a product
        $this->client->request('POST', 'products/create', ['title' => 'random-name', 'description' => 'some-random-desc']);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEquals($count + 1, $this->doctrine->getRepository(Product::class)->count());

        /** @var Product $product */
        $product = $this->doctrine->getRepository(Product::class)->findOneBy([]);

        $this->client = $this->createClient();
        $this->client->request('DELETE', 'products/delete', ['id' => $product->getId()]);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($count, $this->doctrine->getRepository(Product::class)->count());
    }

    /**
     * @test
     */
    public function delete_an_invalid_product_should_throw_exception()
    {
        $count = $this->doctrine->getRepository(Product::class)->count();

        $this->client->request('DELETE', 'products/delete', ['id' => -1]);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($count, $this->doctrine->getRepository(Product::class)->count());
    }

    /**
     * @test
     */
    public function get_a_product_by_its_id_should_return_the_product()
    {
        $count = $this->doctrine->getRepository(Product::class)->count();

        $title = 'random-name';
        $description = 'some-random-desc';
        // there's no factory for entity so let's send a request to create a product
        $this->client->request('POST', 'products/create', compact('title', 'description'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertEquals($count + 1, $this->doctrine->getRepository(Product::class)->count());

        /** @var Product $product */
        $product = $this->doctrine->getRepository(Product::class)->findOneBy([]);

        $this->resetClient();
        $this->client->request('GET', 'products/show/' . $product->getId());
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($data->title, $title);
        $this->assertEquals($data->description, $description);

    }
}