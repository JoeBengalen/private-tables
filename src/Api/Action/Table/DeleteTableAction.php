<?php

namespace JoeBengalen\Tables\Api\Action\Table;

use Exception;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class DeleteTableAction
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
     * DeleteTableAction.
     *
     * @param ApiResponder    $responder
     * @param TableRepository $tableRepository
     */
    public function __construct(
        ApiResponder $responder,
        TableRepository $tableRepository
    ) {
        $this->responder = $responder;
        $this->tableRepository = $tableRepository;
    }

    /**
     * Invoke action.
     *
     * @param Request  $request
     * @param Response $response
     * @param numeric  $tableId
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response, $tableId)
    {
        Assert::isNumeric($tableId);

        try {
            $table = $this->tableRepository->getTableById($tableId);

            $this->tableRepository->deleteTable($table);

            return $this->responder->deleted($response);

        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        }
    }
}
