<?php

namespace JoeBengalen\Tables\Api\Action\Table;

use Exception;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Transformer\TableTransformer;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class GetTableAction
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
     * GetTableAction.
     *
     * @param ApiResponder    $responder
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
            $table = $this->tableRepository->getTableById((int) $tableId);
            $data = $this->tableTransformer->item($table);

            return $this->responder->item($response, $data);

        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        }
    }
}
