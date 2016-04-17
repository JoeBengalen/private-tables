<?php

namespace JoeBengalen\Tables\Api\Action\Table;

use Aura\Filter\Exception\FilterFailed;
use Exception;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Filter\TableFilter;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use JoeBengalen\Tables\Model\Table;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class CreateTableAction
{
    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * @var TableFilter
     */
    protected $tableFilter;

    /**
     * @var TableRepository
     */
    protected $tableRepository;

    /**
     * @var Router
     */
    protected $router;

    /**
     * CreateTableAction.
     *
     * @param ApiResponder    $responder
     * @param TableFilter     $tableFilter
     * @param TableRepository $tableRepository
     */
    public function __construct(
        ApiResponder $responder,
        TableFilter $tableFilter,
        TableRepository $tableRepository,
        Router $router
    ) {
        $this->responder = $responder;
        $this->tableFilter = $tableFilter;
        $this->tableRepository = $tableRepository;
        $this->router = $router;
    }

    /**
     * Invoke action.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response)
    {
        try {
            $body = (array) $request->getParsedBody();
            $this->tableFilter->assert($body);

            $table = new Table(
                null,
                $body['name']
            );

            $tableId = $this->tableRepository->addTable($table);

            $path = $this->router->pathFor('getTable', [
                'tableId' => $tableId
            ]);
            $location = $request->getUri()
                    ->withPath($path)
                    ->withQuery('')
                    ->withFragment('');

            return $this->responder->created($response, (string) $location);

        } catch (FilterFailed $filterFailed) {
            $failures = $filterFailed->getFailures();
            return $this->responder->notValid($response, $failures);
        } catch (DuplicateEntity $duplicateEntity) {
            return $this->responder->duplicate($response, $duplicateEntity);
        }
    }
}
