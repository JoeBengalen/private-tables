<?php

namespace JoeBengalen\Tables\Api;

use Aura\Filter\Failure\FailureCollection;
use Exception;
use InvalidArgumentException;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use Slim\Http\Response;

class ApiResponder
{
    /**
     * Build collection response.
     *
     * @param Response $response
     * @param array[]  $data
     *
     * @return Response
     */
    public function collection(Response $response, array $data)
    {
        return $response->withJson($data, 200, JSON_PRETTY_PRINT);
    }

    /**
     * Build item response.
     *
     * @param Response $response
     * @param array    $data
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function item(Response $response, array $data)
    {
        return $response->withJson($data, 200, JSON_PRETTY_PRINT);
    }

    /**
     * Build created response.
     *
     * @param Response          $response
     * @param string|array|null $data
     *
     * @return Response
     */
    public function created(Response $response, $data = null)
    {
        if (is_array($data)) {
            return $response->withJson($data, 201, JSON_PRETTY_PRINT);
        }

        if (is_string($data)) {
            $response = $response->withHeader('Location', $data);
        }

        return $response->withStatus(201);
    }

    /**
     * Build updated response.
     *
     * @param Response $response
     *
     * @return Response
     */
    public function updated(Response $response, array $data = null)
    {
        if (!is_null($data)) {
            return $this->item($response, $data);
        }

        return $response->withStatus(204);
    }

    /**
     * Build deleted response.
     *
     * @param Response $response
     *
     * @return Response
     */
    public function deleted(Response $response)
    {
        return $response->withStatus(204);
    }

    /**
     * Build not found response.
     *
     * @param Response $response
     *
     * @return Response
     */    
    public function notFound(Response $response)
    {
        $data = [
            'error' => 'Not found',
        ];

        return $response->withJson($data, 404, JSON_PRETTY_PRINT);
    }

    /**
     * Build not valid response.
     *
     * @param Response          $response
     * @param FailureCollection $failures
     *
     * @return Response
     */
    public function notValid(Response $response, FailureCollection $failures)
    {
        return $response->withJson($failures->getMessages(), 400, JSON_PRETTY_PRINT);
    }

    /**
     * Build duplicate response.
     *
     * @param Response        $response
     * @param DuplicateEntity $duplicateEntity
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function duplicate(
        Response $response,
        DuplicateEntity $duplicateEntity
    ) {
        $data = [
            $duplicateEntity->getField() => 'Duplicate value'
        ];

        return $response->withJson($data, 409, JSON_PRETTY_PRINT);
    }

    /**
     * Build error response.
     *
     * @param Response  $response
     * @param Exception $exception
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function error(Response $response, Exception $exception)
    {
        $data = [
            'error' => (string) $exception,
        ];

        return $response->withJson($data, 500, JSON_PRETTY_PRINT);
    }

    /**
     * Build options response.
     *
     * @param Response  $response
     * @param array     $allowedMethods
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function options(Response $response, array $allowedMethods)
    {
        return $response
            ->withHeader('Allow', implode(', ', $allowedMethods))
            ->withJson($allowedMethods, 200, JSON_PRETTY_PRINT);
    }

    /**
     * Build notAllowed response.
     *
     * @param Response  $response
     * @param array     $allowedMethods
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     */
    public function notAllowed(Response $response, array $allowedMethods)
    {
        return $this->options($response, $allowedMethods)->withStatus(405);
    }
}
