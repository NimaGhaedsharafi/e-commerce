<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/10/17
 * Time: 21:58
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\Product;
use AppBundle\Entity\Variant;
use Symfony\Component\HttpFoundation\Response;

class VariantControllerTest extends ApiTest
{
    /**
     * @return Product
     */
    private function createProduct()
    {
        $title = 'some-fancy-title';
        $description = 'some-great-description';

        // there's no factory for entity so let's send a request to create a product
        $this->resetClient();
        $this->client->request('POST', 'products/create', compact('title', 'description'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->resetClient();

        return $this->em->getRepository(Product::class)->findOneBy([], ['id' => 'desc']);
    }

    /**
     * @test
     */
    public function add_a_variant_to_a_product_should_work_fine()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $data = ['color' => 0, 'price' => rand(1, 9) * 1000];
        $url = 'products/%d/variant/add';
        $url = vsprintf($url, [$product->getId()]);

        $this->client->request('POST', $url, $data);
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $this->doctrine->getRepository(Variant::class)->findBy($data));

        $this->seeJsonStructure([
            'variants' => [
                '*' => [
                    'color', 'price'
                ]
            ]
        ], $this->getDecodedResponse());

        $this->assertCount(1, $this->getDecodedResponse()['variants']);
    }

    /**
     * @test
     */
    public function delete_a_variant_from_a_product_should_work_fine()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $data = ['color' => 0, 'price' => rand(1, 9) * 1000];
        $url = 'products/%d/variant/add';
        $url = vsprintf($url, [$product->getId()]);

        $this->client->request('POST', $url, $data);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $variant = $this->getDecodedResponse()['variants'][0];

        $url = 'products/%d/variant/%d/delete';
        $url = vsprintf($url, [$product->getId(), $variant['id']]);

        $this->resetClient();
        $this->client->request('POST', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertCount(0, $this->doctrine->getRepository(Variant::class)->findBy($data));
    }

    /**
     * @test
     */
    public function delete_a_non_exists_variant_from_a_product_should_throw_exception()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $url = 'products/%d/variant/%d/delete';
        $url = vsprintf($url, [$product->getId(), 0]);

        $this->resetClient();
        $this->client->request('POST', $url);
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    /**
     * @test
     */
    public function delete_variant_from_another_product_should_throw_exception()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $data = ['color' => 0, 'price' => rand(1, 9) * 1000];
        $url = 'products/%d/variant/add';
        $url = vsprintf($url, [$product->getId()]);

        $this->client->request('POST', $url, $data);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $variant = $this->getDecodedResponse()['variants'][0];

        /** @var Product $product */
        $product = $this->createProduct();

        $url = 'products/%d/variant/%d/delete';
        $url = vsprintf($url, [$product->getId(), $variant['id']]);

        $this->resetClient();
        $this->client->request('POST', $url);
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->assertCount(1, $this->doctrine->getRepository(Variant::class)->findBy($data));
    }

    /**
     * @test
     */
    public function edit_a_variant_from_a_product_should_work_fine()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $data = ['color' => 0, 'price' => rand(1, 9) * 1000];
        $url = 'products/%d/variant/add';
        $url = vsprintf($url, [$product->getId()]);

        $this->client->request('POST', $url, $data);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $variant = $this->getDecodedResponse()['variants'][0];

        $newPrice = 9999;
        $newColor = 1;
        $data = ['price' => $newPrice, 'color' => $newColor];
        $url = 'products/%d/variant/%d/update';
        $url = vsprintf($url, [$product->getId(), $variant['id']]);

        $this->resetClient();
        $this->client->request('POST', $url, $data);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $data = $this->getDecodedResponse();
        $this->assertEquals($data['price'], $newPrice);
        $this->assertEquals($data['color'], $newColor);
    }

    /**
     * @test
     */
    public function update_a_non_exists_variant_from_a_product_should_throw_exception()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $url = 'products/%d/variant/%d/update';
        $url = vsprintf($url, [$product->getId(), 0]);
        $newPrice = 9999;
        $newColor = 1;

        $this->client->request('POST', $url, ['price' => $newPrice, 'color' => $newColor]);
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    /**
     * @test
     */
    public function update_variant_from_another_product_should_throw_exception()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $data = ['color' => 0, 'price' => rand(1, 9) * 1000];
        $url = 'products/%d/variant/add';
        $url = vsprintf($url, [$product->getId()]);

        $this->client->request('POST', $url, $data);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $variant = $this->getDecodedResponse()['variants'][0];

        /** @var Product $product */
        $product = $this->createProduct();

        $url = 'products/%d/variant/%d/update';
        $url = vsprintf($url, [$product->getId(), $variant['id']]);
        $newPrice = 9999;
        $newColor = 1;

        $this->resetClient();
        $this->client->request('POST', $url, ['price' => $newPrice, 'color' => $newColor]);
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    /**
     * @test
     * @group a
     */
    public function update_a_variant_from_a_product_with_no_data_should_throw_exception()
    {
        /** @var Product $product */
        $product = $this->createProduct();

        $data = ['color' => 0, 'price' => rand(1, 9) * 1000];
        $url = 'products/%d/variant/add';
        $url = vsprintf($url, [$product->getId()]);

        $this->client->request('POST', $url, $data);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $variant = $this->getDecodedResponse()['variants'][0];

        $url = 'products/%d/variant/%d/update';
        $url = vsprintf($url, [$product->getId(), $variant['id']]);
        $newPrice = 9999;
        $newColor = 1;

        $this->resetClient();
        $this->client->request('POST', $url, []);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $this->resetClient();
        $this->client->request('POST', $url, ['color' => $newColor]);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

        $this->resetClient();
        $this->client->request('POST', $url, ['price' => $newPrice]);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode());

    }
}