<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Variant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VariantController extends BaseController
{
    /**
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function addAction($id, Request $request)
    {
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $variant = new Variant();
        $variant->setColor($request->get('color'));
        $variant->setPrice($request->get('price'));
        $variant->setProduct($product);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($variant);
        $manager->flush();

        $product->getVariants()->add($variant);

        return $this->response($product, Response::HTTP_CREATED);
    }
}
