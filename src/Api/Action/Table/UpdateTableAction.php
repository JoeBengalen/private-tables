<?php

namespace JoeBengalen\Tables\Api\Action\Table;

use Aura\Filter\Exception\FilterFailed;
use Exception;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Filter\TableFilter;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class UpdateTableAction
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
     * CreateTableAction.
     *
     * @param ApiResponder   $responder
     * @param TableFilter     $tableFilter
     * @param TableRepository $tableRepository
     */
    public function __construct(
        ApiResponder $responder,
        TableFilter $tableFilter,
        TableRepository $tableRepository
    ) {
        $this->responder = $responder;
        $this->tableFilter = $tableFilter;
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
            $body = (array) $request->getParsedBody();
            $this->tableFilter->assert($body);

            $table = $this->tableRepository->getTableById((int) $tableId);
            $table->setName($body['name']);

            $this->tableRepository->updateTable($table);

            return $this->responder->updated($response);

        } catch (FilterFailed $filterFailed) {
            $failures = $filterFailed->getFailures();
            return $this->responder->notValid($response, $failures);
        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        } catch (DuplicateEntity $duplicateEntity) {
            return $this->responder->duplicate($response, $duplicateEntity);
        }
    }
}
