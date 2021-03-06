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

        $this->assertTrue($this->client->getResponse()->isOk());

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
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->assertEquals($count, $this->doctrine->getRepository(Product::class)->count());
    }

    /**
     * @test
     */
    public function get_a_product_by_its_id_should_return_the_product()
    {
        $title = 'random-name';
        $description = 'some-random-desc';
        // there's no factory for entity so let's send a request to create a product
        $this->client->request('POST', 'products/create', compact('title', 'description'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        /** @var Product $product */
        $product = $this->doctrine->getRepository(Product::class)->findOneBy([]);

        $this->resetClient();
        $this->client->request('GET', 'products/show/' . $product->getId());
        $this->assertTrue($this->client->getResponse()->isOk());

        $data = $this->getDecodedResponse();
        $this->assertEquals($data['title'], $title);
        $this->assertEquals($data['description'], $description);
    }

    /**
     * @test
     */
    public function get_a_product_by_an_invalid_id_should_throw_exception()
    {
        $this->client->request('GET', 'products/show/' . 0);
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    /**
     * @test
     */
    public function edit_a_product_should_work_fine()
    {
        $title = 'random-name';
        $description = 'some-random-desc';
        // there's no factory for entity so let's send a request to create a product
        $this->client->request('POST', 'products/create', compact('title', 'description'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $count = $this->doctrine->getRepository(Product::class)->count();

        /** @var Product $product */
        $product = $this->doctrine->getRepository(Product::class)->findOneBy([]);

        $newTitle = 'a-new-title';
        $newDescription = 'a-new-description';
        $data = [
            'title' => $newTitle,
            'description' => $newDescription
        ];

        $this->resetClient();
        $this->client->request('POST', 'products/edit/' . $product->getId(), $data);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($count, $this->doctrine->getRepository(Product::class)->count());

        $this->resetClient();
        $this->client->request('GET', 'products/show/' . $product->getId());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $data = $this->getDecodedResponse();
        $this->assertEquals($data['title'], $newTitle);
        $this->assertEquals($data['description'], $newDescription);
    }

    /**
     * @test
     */
    public function edit_an_non_existing_product_should_throw_exception()
    {
        $newTitle = 'a-new-title';
        $newDescription = 'a-new-description';
        $data = [
            'title' => $newTitle,
            'description' => $newDescription
        ];

        $this->client->request('POST', 'products/edit/' . 0, $data);
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    /**
     * @test
     */
    public function create_product_without_proper_input_should_throw_exception()
    {
        $count = $this->doctrine->getRepository(Product::class)->count();

        $data = [];
        $this->client->request('POST', 'products/create', $data);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $this->resetClient();
        $data = ['title' => 'random-name'];
        $this->client->request('POST', 'products/create', $data);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $this->resetClient();
        $data = ['description' => 'some-random-desc'];
        $this->client->request('POST', 'products/create', $data);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($count, $this->doctrine->getRepository(Product::class)->count());
    }
}