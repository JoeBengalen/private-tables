<?php

namespace JoeBengalen\RamlApiTester;

use Raml\ApiDefinition;
use Raml\Method;
use Raml\Parser;
use Raml\Resource;

class TestCaseBuilder
{
    /**
     * @var ApiDefinition 
     */
    protected $api;

    /**
     * @var WrappedResource[] 
     */
    protected $resources;

    /**
     * @var TestCase[]
     */
    protected $testCases;

    public function __construct($raml)
    {
        $parser = new Parser();

        $this->api = $parser->parse($raml);
    }

    /**
     * 
     * @param Resource[]           $resources
     * @param WrappedResource|null $parent
     */
    protected function extractResources(array $resources, WrappedResource $parent = null)
    {
        foreach ($resources as $resource) {
            $wrappedResource = new WrappedResource($resource, $parent);
            $this->resources[] = $wrappedResource;

            $this->extractResources($resource->getResources(), $wrappedResource);
        }
    }

    protected function prefixUri($uri)
    {
        return rtrim($this->api->getBaseUrl(), '/') . $uri;
    }

    /**
     * 
     * @param WrappedResource $resource
     *
     * @return string[]
     */
    protected function createResourceUris(WrappedResource $resource)
    {
        $uriParams = $this->buildUriParams($resource);

        if (empty($uriParams)) {
            return (array) $this->prefixUri($resource->getResource()->getUri());
        }

        return array_map(
            function (array $params) use ($resource) {
                $uri = strtr($resource->getResource()->getUri(), $params);
                return $this->prefixUri($uri);
            },
            $uriParams
        );
    }

    /**
     * 
     * @param WrappedResource $resource
     *
     * @return array[]
     */
    private function buildUriParams(WrappedResource $resource)
    {
        $uriParameters = $resource->getResource()->getUriParameters();
        $parentParams = $this->buildParentUriParams($resource);

        if (empty($uriParameters) && empty($parentParams)) {
            return [];
        }

        $uriParams = [];
        foreach ($uriParameters as $param) {
            foreach ($param->getExamples() as $uriIndex => $example) {
                $uriParams[$uriIndex]["{{$param->getKey()}}"] = $example;
            }
        }

        if (empty($uriParams)) {
            return [$parentParams];
        }

        foreach ($uriParams as $uriIndex => $params) {
            $uriParams[$uriIndex] = array_merge($parentParams, $params);
        }

        return $uriParams;
    }

    /**
     * 
     * @param WrappedResource $resource
     *
     * @return array
     */
    private function buildParentUriParams(WrappedResource $resource)
    {
        $parentParams = [];
        $parent = $resource->getParent();

        while ($parent) {
            foreach ($parent->getResource()->getUriParameters() as $param) {
                $parentParams["{{$param->getKey()}}"] = $param->getExample();
            }

            $parent = $parent->getParent();
        }
        
        return $parentParams;
    }

    protected function createGetCases(WrappedResource $resource, Method $method)
    {
        $uris = $this->createResourceUris($resource);

        foreach ($method->getResponses() as $response) {
            if (empty($response->getBodies())) {
                $testCase = new Testcase();
                $testCase->request = new Request();
                $testCase->response = new Response();

                $testCase->description = $method->getDescription();

                $testCase->request->method = $method->getType();
                $testCase->request->uri = array_shift($uris);

                $testCase->request->headers = $method->getHeaders();
                $testCase->request->params = $method->getQueryParameters();

                $testCase->response->code = $response->getStatusCode();
                $testCase->response->headers = $response->getHeaders();

                $this->testCases[] = $testCase;
            }

            foreach ($response->getBodies() as $responseBody) {

                $testCase = new Testcase();
                $testCase->request = new Request();
                $testCase->response = new Response();

                $testCase->description = $method->getDescription();

                $testCase->request->method = $method->getType();
                $testCase->request->uri = array_shift($uris);

                $testCase->request->acceptType = $responseBody->getMediaType();
                $testCase->request->headers = $method->getHeaders();
                $testCase->request->params = $method->getQueryParameters();

                $testCase->response->code = $response->getStatusCode();
                $testCase->response->headers = $response->getHeaders();
                $testCase->response->contentType = $responseBody->getMediaType();
                $testCase->response->schema = $responseBody->getSchema();

                $this->testCases[] = $testCase;
            }
        }
    }

    protected function createPostCases(WrappedResource $resource, Method $method)
    {
        $uris = $this->createResourceUris($resource);
        $uri = $uris[0];

        foreach ($method->getBodies() as $requestBody) {
            $requestBodyExamples = $requestBody->getExamples();

            foreach ($method->getResponses() as $response) {

                $testCase = new TestCase();
                $testCase->request = new Request();
                $testCase->response = new Response();

                $testCase->description = $method->getDescription();

                $testCase->request->method = $method->getType();
                $testCase->request->uri = $uri;

                $testCase->request->headers = $method->getHeaders();
                $testCase->request->params = $method->getQueryParameters();

                $testCase->request->contentType = $requestBody->getMediaType();
                $testCase->request->body = array_shift($requestBodyExamples);

                $testCase->response->code = $response->getStatusCode();
                $testCase->response->headers = $response->getHeaders();

                $this->testCases[] = $testCase;
            }
        }
    }

    protected function createPutCases(WrappedResource $resource, Method $method)
    {
        $uris = $this->createResourceUris($resource);
        $okUri = $uris[0];
        $nokUri = $uris[1];
        foreach ($method->getBodies() as $requestBody) {
            $requestBodyExamples = $requestBody->getExamples();

            foreach ($method->getResponses() as $response) {
                $url = $response->getStatusCode() === 404 ? $nokUri : $okUri;

                if (empty($response->getBodies())) {
                    $testCase = new Testcase();
                    $testCase->request = new Request();
                    $testCase->response = new Response();

                    $testCase->description = $method->getDescription();

                    $testCase->request->method = $method->getType();
                    $testCase->request->uri = $url;

                    $testCase->request->headers = $method->getHeaders();
                    $testCase->request->params = $method->getQueryParameters();

                    $testCase->request->contentType = $requestBody->getMediaType();
                    $testCase->request->body = array_shift($requestBodyExamples);

                    $testCase->response->code = $response->getStatusCode();
                    $testCase->response->headers = $response->getHeaders();

                    $this->testCases[] = $testCase;
                }

                foreach ($response->getBodies() as $responseBody) {

                    $testCase = new Testcase();
                    $testCase->request = new Request();
                    $testCase->response = new Response();

                    $testCase->description = $method->getDescription();

                    $testCase->request->method = $method->getType();
                    $testCase->request->uri = $url;

                    $testCase->request->acceptType = $responseBody->getMediaType();
                    $testCase->request->headers = $method->getHeaders();
                    $testCase->request->params = $method->getQueryParameters();

                    $testCase->request->contentType = $requestBody->getMediaType();
                    $testCase->request->body = array_shift($requestBodyExamples);

                    $testCase->response->code = $response->getStatusCode();
                    $testCase->response->headers = $response->getHeaders();
                    $testCase->response->contentType = $responseBody->getMediaType();
                    $testCase->response->schema = $responseBody->getSchema();

                    $this->testCases[] = $testCase;
                }
            }
        }
    }

    protected function createDeleteCases(WrappedResource $resource, Method $method)
    {
        $uris = $this->createResourceUris($resource);
        $delUri = $uris[2];
        $notFoundUrl = $uris[1];

        foreach ($method->getResponses() as $response) {
            if (empty($response->getBodies())) {
                $testCase = new Testcase();
                $testCase->request = new Request();
                $testCase->response = new Response();

                $testCase->description = $method->getDescription();

                $testCase->request->method = $method->getType();
                $testCase->request->uri = $delUri;

                $testCase->request->headers = $method->getHeaders();
                $testCase->request->params = $method->getQueryParameters();

                $testCase->response->code = $response->getStatusCode();
                $testCase->response->headers = $response->getHeaders();

                $this->testCases[] = $testCase;
            }

            foreach ($response->getBodies() as $responseBody) {

                $testCase = new Testcase();
                $testCase->request = new Request();
                $testCase->response = new Response();

                $testCase->description = $method->getDescription();

                $testCase->request->method = $method->getType();
                $testCase->request->uri = $notFoundUrl;

                $testCase->request->acceptType = $responseBody->getMediaType();
                $testCase->request->headers = $method->getHeaders();
                $testCase->request->params = $method->getQueryParameters();

                $testCase->response->code = $response->getStatusCode();
                $testCase->response->headers = $response->getHeaders();
                $testCase->response->contentType = $responseBody->getMediaType();
                $testCase->response->schema = $responseBody->getSchema();

                $this->testCases[] = $testCase;
            }
        }
    }

    public function build()
    {
        $this->extractResources($this->api->getResources());
        
        //var_dump($this->resources);



        foreach ($this->resources as $resource) {
            foreach ($resource->getResource()->getMethods() as $method) {
                if ($method->getType() == 'GET') {
                    $this->createGetCases($resource, $method);
                }
                if ($method->getType() == 'POST') {
                    $this->createPostCases($resource, $method);
                }
                if ($method->getType() == 'PUT') {
                    $this->createPutCases($resource, $method);
                }
                if ($method->getType() == 'DELETE') {
                    $this->createDeleteCases($resource, $method);
                }
            }
        }
    }

    public function getTestCases()
    {
        return $this->testCases;
    }
}
