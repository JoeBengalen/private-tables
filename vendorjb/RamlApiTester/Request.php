<?php

namespace JoeBengalen\RamlApiTester;

class Request
{
    public $method;
    public $uri;
    public $contentType;
    public $acceptType;
    public $headers = [];
    public $params = [];
    public $body;
}
