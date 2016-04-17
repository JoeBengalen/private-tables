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

class GetFieldAction
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
     * @param numeric  $fieldId
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $tableId, $fieldId)
    {
        Assert::isNumeric($tableId);
        Assert::isNumeric($fieldId);

        try {
            $table = $this->tableRepository->getTableById($tableId);
            $field = $this->fieldRepository->getFieldById($fieldId);

            if ($table->getId() !== $field->getTableId()) {
                return $this->responder->notFound($response);
            }

            $data = $this->fieldTransformer->item($field);

            return $this->responder->item($response, $data);

        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        } catch (Exception $exception) {
            return $this->responder->error($response, $exception);
        }
    }
}
