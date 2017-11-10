<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/7/17
 * Time: 21:28
 */

namespace Tests\AppBundle\Controller;



use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Client;

abstract class ApiTest extends WebTestCase
{
    /** @var ContainerBuilder */
    protected $container;
    /** @var ManagerRegistry */
    protected $doctrine;
    /** @var Client */
    protected $client;
    /** @var EntityManager */
    protected $em;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->doctrine = $this->container->get('doctrine');
        $this->client = $this->createClient(['environment' => 'test']);
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Client $client
     * @return mixed
     */
    public function getDecodedResponse($client = null)
    {
        if ($client === null) {
            $client = $this->client;
        }
        return json_decode($client->getResponse()->getContent(), true);
    }
    /**
     * Assert that the JSON response has a given structure.
     *
     * @param  array|null  $structure
     * @param  array|null  $responseData
     * @return $this
     */
    public function seeJsonStructure(array $structure = null, $responseData = null)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $responseData);
                foreach ($responseData as $responseDataItem) {
                    $this->seeJsonStructure($structure['*'], $responseDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $responseData);
                $this->seeJsonStructure($structure[$key], $responseData[$key]);
            } else {
                $this->assertArrayHasKey($value, $responseData);
            }
        }
        return $this;
    }

    public function resetClient()
    {
        $this->client = $this->createClient();
    }
}