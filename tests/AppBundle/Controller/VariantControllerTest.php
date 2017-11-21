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
        $this->client->request('POST', 'products/create', compact('title', 'description'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->resetClient();

        return $this->em->getRepository(Product::class)->findOneBy([]);
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
}