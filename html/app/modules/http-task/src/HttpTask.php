<?php

namespace matpoppl\HttpCronTask;

use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use matpoppl\HttpClient\HttpClient;
use matpoppl\HttpMessage\Request;
use matpoppl\HttpMessage\Uri;
use matpoppl\Cron\StepData;
use matpoppl\Cron\Entity\TaskEntity;

class HttpTask
{
    /** @var ContainerInterface */
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function createRequest(TaskEntity $entity, StepData $ctx)
    {
        $uri = new Uri($entity->getParam('url'));
        
        $uri = $uri->withQuery(implode('&', array_filter([
            $uri->getQuery(),
            $entity->getParam('query'),
            $ctx->getParam('query'),
        ])));
        
        $body = $ctx->getParam('body') ?: $entity->getParam('body');
        
        return Request::createFromArgs($entity->getParam('method'), '' . $uri, $entity->getHeadersAsArray(), $body);
    }
    
    public function createHttpClient(TaskEntity $entity) : ClientInterface
    {
        $options = array_filter($entity->getParam('ssl'), 'strlen');
        return new HttpClient($options);
    }
    
    public function run(TaskEntity $entity, StepData $ctx)
    {
        $req = $this->createRequest($entity, $ctx);
        
        try {
            $res = $this->createHttpClient($entity)->sendRequest($req);
        } catch (\Exception $ex) {
            // @TODO handle client exception
            throw $ex;
        }

        return $this->processResponse($res, $ctx);
    }
    
    public function processResponse(ResponseInterface $res, StepData $ctx)
    {
        if (false === strpos($res->getHeaderLine('Content-Type'), 'application/json')) {
            throw new \ErrorException('NotImplemented handle non-json');
        }
        
        return $this->processJsonResponse($res, $ctx);
    }
    
    public function processJsonResponse(ResponseInterface $res, StepData $ctx)
    {
        $json = json_decode(''.$res->getBody(), true);
        
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \ErrorException('NotImplemented handle json parse-error');
        }
        
        //$status = 200 === $res->getStatusCode() ? $json->status : 'error';
        
        $ret = new StepData();
        $ret->status = $json['status'] ?? null;
        $ret->iteration = 1 + $ctx->iteration;
        if (isset($json['params'])) {
            $ret->setParams($json['params']);
        }
        
        return $ret;
    }
}
