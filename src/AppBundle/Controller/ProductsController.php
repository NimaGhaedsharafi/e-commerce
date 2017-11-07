<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/5/17
 * Time: 23:04
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Product;

class ProductsController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->response($this->getDoctrine()->getRepository(Product::class)->findAll());
    }
    }
}