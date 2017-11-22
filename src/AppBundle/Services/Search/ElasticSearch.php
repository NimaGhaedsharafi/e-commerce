<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/22/17
 * Time: 13:40
 */

namespace AppBundle\Services\Search;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ElasticSearch
 * @package AppBundle\Service\Search
 */
class ElasticSearch implements SearchService
{
    /** @var Client */
    private $client;

    /**
     * ElasticSearch constructor.
     * @param Client $client
     */
    public function __construct(ContainerInterface $container)
    {
        $this->client = $this->createClient($container);
    }

    /**
     * @param ContainerInterface $container
     * @return Client
     */
    private function createClient($container)
    {
        $elastic = $container->getParameter('elastic.host');

        return ClientBuilder::create()
            ->setConnectionParams(['headers' => ['content-type' => ['application/json']]])
            ->setHosts(explode(',', $elastic))->build();
    }

    /**
     * @param $keyword
     * @return array
     */
    public function search($keyword): array
    {
        try {
            return $this->client->search([
                'index' => 'products',
                'type' => 'product',
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                [
                                    'match' => [
                                        'title' => $keyword,
                                    ]
                                ],
                                [
                                    'match' => [
                                        'variants' => $keyword,
                                    ]
                                ],
                                [
                                    'match' => [
                                        'description' => $keyword,
                                    ]
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {}
        return [];
    }

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    public function index($id, array $data): bool
    {
        $result = $this->client->index([
            'index' => 'products',
            'type' => 'product',
            'id' => $id,
            'body' => $data
        ]);

        return empty($result) == false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->client->delete([
            'index' => 'products',
            'type' => 'product',
            'id' => $id
        ]);
        
        return empty($result) == false;
    }
}