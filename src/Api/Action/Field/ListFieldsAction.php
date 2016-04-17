<?php

namespace JoeBengalen\Tables\Api\Action\Field;

use Exception;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Transformer\FieldTransformer;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\FieldRepository;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class ListFieldsAction
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
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * @var FieldTransformer
     */
    protected $fieldTransformer;

    /**
     * ListFieldsAction.
     *
     * @param ApiResponder     $responder
     * @param TableRepository  $tableRepository
     * @param FieldRepository  $fieldRepository
     * @param FieldTransformer $fieldTransformer
     */
    public function __construct(
        ApiResponder $responder,
        TableRepository $tableRepository,
        FieldRepository $fieldRepository,
        FieldTransformer $fieldTransformer
    ) {
        $this->responder = $responder;
        $this->tableRepository = $tableRepository;
        $this->fieldRepository = $fieldRepository;
        $this->fieldTransformer = $fieldTransformer;
    }

    /**
     * Invoke action.
     *
     * @param Request  $request
     * @param Response $response
     * @param numeric  $tableId
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $tableId)
    {
        Assert::isNumeric($tableId);

        try {
            $table = $this->tableRepository->getTableById($tableId);
            $fieldCollection = $this->fieldRepository->getFieldsByTable($table);
            $data = $this->fieldTransformer->collection($fieldCollection);

            return $this->responder->collection($response, $data);

        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        } catch (Exception $exception) {
            return $this->responder->error($response, $exception);
        }
    }
}
