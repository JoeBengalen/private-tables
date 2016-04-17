<?php

namespace JoeBengalen\RamlApiTester;

use Raml\Schema\Definition\JsonSchemaDefinition;

class Response
{
    public $code;
    public $headers = [];
    /**
     * @var JsonSchemaDefinition|null
     */
    public $schema;
    public $contentType;
}
