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
use AppBundle\Exception\ValidationFailed;
use AppBundle\Services\Search\SearchService;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductsController
 * @package AppBundle\Controller
 */
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

         if ($request->get('title') === null || $request->get('description') === null) {
             throw new ValidationFailed();
         }

        $product = new Product();
        $product->setTitle($request->get('title'));
        $product->setDescription($request->get('description'));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($product);
        $manager->flush();

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        $searchService->index($product->getId(), $product->toArray());

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
        $productId = $product->getId();
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        $searchService->delete($productId);
        
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

    /**
     * @param $id
     * @param Request $request
     * @return Response
     */
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

        /** @var SearchService $searchService */
        $searchService = $this->container->get(SearchService::class);
        $searchService->index($product->getId(), $product->toArray());

        return $this->response($product);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function searchAction(Request $request)
    {
        if ($request->get('keyword') === null) {
            throw new ValidationFailed();
        }

        /** @var \Closure $search
         * @return array
         */
        $search = function ($keyword) {
            /** @var AdapterInterface $cache */
            $cache = $this->get('cache.app');
            $cached = $cache->getItem($keyword);

            if ($cached->isHit() == false) {
                /** @var SearchService $searchService */
                $searchService = $this->container->get(SearchService::class);
                $response = $searchService->search($keyword);
                $cached->set($response)->expiresAfter(120);
                $this->get('cache.app')->save($cached);

                return $response;
            }
            return $cached->get();
        };

        $response = $search(trim($request->get('keyword')));
        if (count($response) == 0) {
            return $this->ack();
        }

        $collection = [];
        foreach ($response['hits']['hits'] as $hit) {
            $collection[] = [
                'title' => $hit['_source']['title'],
                'description' => $hit['_source']['description'],
                'variants' => $hit['_source']['variants'],
            ];
        }

        return $this->response($collection);
    }
}