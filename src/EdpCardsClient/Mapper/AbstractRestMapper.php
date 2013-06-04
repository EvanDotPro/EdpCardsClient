<?php
namespace EdpCardsClient\Mapper;

use Zend\ServiceManager as SM;
use Zend\Json\Json;
use Zend\Http\Client as HttpClient;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class AbstractRestMapper implements SM\ServiceLocatorAwareInterface
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var SM\ServiceLocatorInterface
     */
    protected $serviceLocator = null;


    public function __construct($baseUri)
    {
        $this->baseUri = $baseUri;
    }

    protected function request($path, $method = 'GET', $data = false)
    {
        $uri = $this->baseUri . $path;
        $client = $this->getHttpClient();
        $client->setUri($uri);

        if ($method != 'GET') {
            $client->setMethod($method);
        }

        if ($data) {
            $client->setParameterPost($data);
        }

        $response = $client->send();
        if (!$response->isSuccess()) {
            return false;
        }

        try {
            return Json::decode($response->getBody(), true);
        } catch (\Exception $e) {
            echo $response->getBody();
            die();
        }
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (!$this->httpClient) {
            $this->httpClient = new HttpClient;
        }

        return $this->httpClient;
    }

    /**
     * @param HttpClient $httpClient
     * @return AbstractRestMapper
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods();
        }

        return $this->hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return AbstractRestMapper
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Set service locator
     *
     * @param SM\ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(SM\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return SM\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
