<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Variant;
use AppBundle\Exception\NotFoundEntity;
use AppBundle\Exception\ValidationFailed;
use AppBundle\Services\Search\SearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VariantController
 * @package AppBundle\Controller
 */
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

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        $searchService->index($product->getId(), $product->toArray());

        return $this->response($product, Response::HTTP_CREATED);
    }

    /**
     * @param $pid
     * @param $vid
     * @return Response
     */
    public function deleteAction($pid, $vid)
    {
        /** @var Variant $variant */
        $variant = $this->getDoctrine()->getRepository(Variant::class)->find($vid);

        if ($variant === null || $variant->getProductId() != $pid) {
            throw new NotFoundEntity();
        }

        $product = $variant->getProduct();
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($variant);
        $manager->flush();

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        $searchService->index($product->getId(), $product->toArray());

        return $this->ack();
    }

    /**
     * @param $pid
     * @param $vid
     * @param Request $request
     * @return Response
     */
    public function updateAction($pid, $vid, Request $request)
    {
        // TODO: add a validation
        // it needs some validation but I don't know how to do it in right way!
        // but just for now let's check their existence
         if ($request->get('price') === null || $request->get('color') === null) {
             throw new ValidationFailed();
         }

        /** @var Variant $variant */
        $variant = $this->getDoctrine()->getRepository(Variant::class)->find($vid);

        if ($variant === null || $variant->getProductId() != $pid) {
            throw new NotFoundEntity();
        }
        $variant->setColor($request->get('color'));
        $variant->setPrice($request->get('price'));
        $this->getDoctrine()->getManager()->flush();

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        $searchService->index($variant->getProductId(), $variant->getProduct()->toArray());

        return $this->response($variant);
    }
}
