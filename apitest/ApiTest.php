<?php

namespace JoeBengalen\RamlApiTester\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use JoeBengalen\RamlApiTester\PHPUnitTestCase;
use JoeBengalen\RamlApiTester\RequestResponseLogger;

class ApiTest extends PHPUnitTestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        RequestResponseLogger::setTargetDir('C:\Development\Projects\php\Tables/apitest/_out/');

        $this->client = new Client([
            'http_errors' => false,
        ]);
    }

    public function testGetAListOfTables()
    {
        $request = new Request(
            'GET',
            'http://admin:Admin!23@localhost:8000/api/v1/tables',
            array (
  'Accept' => 'application/json',
),
            NULL
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(200, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"array","items":{"type":"object","properties":{"id":{"type":"integer"},"name":{"type":"string"}},"required":["id","name"]},"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\tables.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testAddANewTable()
    {
        $request = new Request(
            'POST',
            'http://admin:Admin!23@localhost:8000/api/v1/tables',
            array (
  'Content-Type' => 'application/json',
),
            '{"name":"newtable","id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\table-create.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(201, $response->getStatusCode());

        
        
    }

    public function testAddANewTableBadRequest()
    {
        $request = new Request(
            'POST',
            'http://admin:Admin!23@localhost:8000/api/v1/tables',
            array (
  'Content-Type' => 'application/json',
),
            '{}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(400, $response->getStatusCode());

        
        
    }

    public function testAddANewTableConflict()
    {
        $request = new Request(
            'POST',
            'http://admin:Admin!23@localhost:8000/api/v1/tables',
            array (
  'Content-Type' => 'application/json',
),
            '{"name":"table2","id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\table-conflict.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(409, $response->getStatusCode());

        
        
    }

    public function testGetATableByItsTableId()
    {
        $request = new Request(
            'GET',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1',
            array (
  'Accept' => 'application/json',
),
            NULL
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(200, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"object","properties":{"id":{"type":"integer"},"name":{"type":"string"}},"required":["id","name"],"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\table.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testGetATableByItsTableIdNotFound()
    {
        $request = new Request(
            'GET',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/99999',
            array (
  'Accept' => 'application/json',
),
            NULL
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(404, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"object","properties":{"error":{"type":"string"},"details":{"type":"array","items":{"type":"string"}}},"required":["error"],"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\error.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testUpdateATableByItsTableId()
    {
        $request = new Request(
            'PUT',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1',
            array (
  'Content-Type' => 'application/json',
),
            '{"name":"updatedtable","id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\table-update.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(204, $response->getStatusCode());

        
        
    }

    public function testUpdateATableByItsTableIdBadRequest()
    {
        $request = new Request(
            'PUT',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1',
            array (
  'Accept' => 'application/json',
  'Content-Type' => 'application/json',
),
            '{}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(400, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"object","properties":{"name":{"type":"array","items":{"type":"string"}}},"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\table-badrequest.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testUpdateATableByItsTableIdConflict()
    {
        $request = new Request(
            'PUT',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1',
            array (
  'Accept' => 'application/json',
  'Content-Type' => 'application/json',
),
            '{"name":"table2","id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\table-conflict.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(409, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"object","properties":{"name":{"type":"string"}},"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\table-conflict.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testUpdateATableByItsTableIdNotFound()
    {
        $request = new Request(
            'PUT',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/99999',
            array (
  'Accept' => 'application/json',
  'Content-Type' => 'application/json',
),
            '{"name":"updatedtable","id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\table-update.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(404, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"object","properties":{"error":{"type":"string"},"details":{"type":"array","items":{"type":"string"}}},"required":["error"],"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\error.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testDeleteATableByItsTableId()
    {
        $request = new Request(
            'DELETE',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/2',
            array (
),
            NULL
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(204, $response->getStatusCode());

        
        
    }

    public function testDeleteATableByItsTableIdNotFound()
    {
        $request = new Request(
            'DELETE',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/99999',
            array (
  'Accept' => 'application/json',
),
            NULL
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(404, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http://json-schema.org/draft-04/schema","type":"object","properties":{"error":{"type":"string"},"details":{"type":"array","items":{"type":"string"}}},"required":["error"],"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\error.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testGetAListOfFields()
    {
        $request = new Request(
            'GET',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1/fields',
            array (
  'Accept' => 'application/json',
),
            NULL
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(200, $response->getStatusCode());

                $this->assertContentType(
            'application/json',
            $response->getHeaderLine('Content-Type')
        );
        
                $this->assertJsonMatchesSchema(
            '{"$schema":"http: //json-schema.org/draft-04/schema#","type":"array","items":{"type":"object","properties":{"id":{"type":"integer"},"tableId":{"type":"integer"},"name":{"type":"string"},"type":{"type":"string"},"length":{"type":["integer","null"]},"allowNull":{"type":"boolean"},"default":{"type":["string","null"]},"comment":{"type":["string","null"]},"isPrimaryKey":{"type":"boolean"},"autoIncrement":{"type":"boolean"}},"required":["id","tableId","name","type","length","allowNull","default","comment","isPrimaryKey","autoIncrement"]},"id":"file://C:\\\Development\\\Projects\\\php\\\Tables\\\raml\\\schema\\\fields.json"}',
            (string) $response->getBody()
        );
        
    }

    public function testAddANewField()
    {
        $request = new Request(
            'POST',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1/fields',
            array (
  'Content-Type' => 'application/json',
),
            '{"name":"newfield","type":"STRING","length":null,"allowNull":false,"default":null,"comment":null,"isPrimaryKey":false,"autoIncrement":false,"id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\field-create.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(201, $response->getStatusCode());

        
        
    }

    public function testAddANewFieldBadRequest()
    {
        $request = new Request(
            'POST',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1/fields',
            array (
  'Content-Type' => 'application/json',
),
            '{}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(400, $response->getStatusCode());

        
        
    }

    public function testAddANewFieldConflict()
    {
        $request = new Request(
            'POST',
            'http://admin:Admin!23@localhost:8000/api/v1/tables/1/fields',
            array (
  'Content-Type' => 'application/json',
),
            '{"name":"fieldname","type":"STRING","length":null,"allowNull":false,"default":null,"comment":null,"isPrimaryKey":false,"autoIncrement":false,"id":"file:\\/\\/C:\\\\Development\\\\Projects\\\\php\\\\Tables\\\\raml\\\\sample\\\\field-conflict.json"}'
        );

        $response = $this->client->send($request);

        RequestResponseLogger::logRequestResponse(__FUNCTION__, $request, $response);

        $this->assertSame(409, $response->getStatusCode());

        
        
    }
}
