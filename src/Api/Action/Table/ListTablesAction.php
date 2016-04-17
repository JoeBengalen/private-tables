<?php

namespace JoeBengalen\Tables\Api\Action\Table;

use Exception;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Transformer\TableTransformer;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class ListTablesAction
{
    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * @var TableRepository
     */
    protected $tableRepository;

    /**
     * @var TableTransformer
     */

    protected $tableTransformer;

    /**
     * ListTablesAction.
     *
     * @param ApiResponder     $responder
     * @param TableRepository  $tableRepository
     * @param TableTransformer $tableTransformer
     */
    public function __construct(
        ApiResponder $responder,
        TableRepository $tableRepository,
        TableTransformer $tableTransformer
    ) {
        $this->responder = $responder;
        $this->tableRepository = $tableRepository;
        $this->tableTransformer = $tableTransformer;
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
        $tableCollection = $this->tableRepository->getTables();
        $data = $this->tableTransformer->collection($tableCollection);

        return $this->responder->collection($response, $data);
    }
}
