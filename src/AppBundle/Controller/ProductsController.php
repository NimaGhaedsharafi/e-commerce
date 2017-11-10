<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/5/17
 * Time: 23:04
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use AppBundle\Exception\NotFoundEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->response($this->getDoctrine()->getRepository(Product::class)->findAll());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        // TODO: add a validation
        // it needs some validation but I don't know how to do it in right way!
        // but just for now let's check their existence

        // if ($request->get('title') === null || $request->get('description') === null) {
        //     throw new ValidationFailed();
        // }

        $product = new Product();
        $product->setTitle($request->get('title'));
        $product->setDescription($request->get('description'));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($product);
        $manager->flush();

        return $this->response($product, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $request->get('id', 0)]);

        if ($product === null) {
            throw new NotFoundEntity();
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();
        
        return $this->ack();
    }

    /**
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $id]);

        if ($product === null) {
            throw new NotFoundEntity();
        }

        return $this->response($product);
    }

    public function updateAction($id, Request $request)
    {
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null) {
            throw new NotFoundEntity();
        }

        $product->setTitle($request->get('title'));
        $product->setDescription($request->get('description'));
        $this->getDoctrine()->getManager()->flush();
        
        return $this->response($product);
    }
}