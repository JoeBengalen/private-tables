<?php

namespace JoeBengalen\RamlApiTester;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestResponseLogger
{
    protected static $targetDir;

    public static function setTargetDir($targetDir)
    {
        self::$targetDir = $targetDir;
    }

    public static function logRequestResponse(
        $filename,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $file = self::$targetDir . "{$filename}.json";

        $jsonOptions = JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE;

        $data = [
            'request' => [
                'method' => $request->getMethod(),
                'uri' => (string) $request->getUri(),
                'headers' => $request->getHeaders(),
                'body' => json_decode((string) $request->getBody()),
                'bodyRaw' => (string) $request->getBody(),
            ],
            'response' => [
                'statusCode' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => json_decode((string) $response->getBody()),
                'bodyRaw' => (string) $response->getBody(),
            ],
        ];

        file_put_contents($file, json_encode($data, $jsonOptions));
    }
}
