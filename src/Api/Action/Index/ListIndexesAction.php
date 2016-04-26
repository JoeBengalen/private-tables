<?php

namespace JoeBengalen\Tables\Api\Action\Index;

use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Transformer\IndexTransformer;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\FieldRepository;
use JoeBengalen\Tables\Model\IndexRepository;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class ListIndexesAction
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
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     * @var IndexTransformer
     */

    protected $indexTransformer;

    /**
     * ListIndexesAction.
     *
     * @param ApiResponder     $responder
     * @param TableRepository  $tableRepository
     * @param FieldRepository  $fieldRepository
     * @param IndexRepository  $indexRepository
     * @param IndexTransformer $indexTransformer
     */
    public function __construct(
        ApiResponder $responder,
        TableRepository $tableRepository,
        FieldRepository $fieldRepository,
        IndexRepository $indexRepository,
        IndexTransformer $indexTransformer
    ) {
        $this->responder = $responder;
        $this->tableRepository = $tableRepository;
        $this->fieldRepository = $fieldRepository;
        $this->indexRepository = $indexRepository;
        $this->indexTransformer = $indexTransformer;
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
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(
        Request $request,
        Response $response,
        $tableId, 
        $fieldId
    ) {
        Assert::isNumeric($tableId);
        Assert::isNumeric($fieldId);

        try {
            $table = $this->tableRepository->getTableById($tableId);
            $field = $this->fieldRepository->getFieldById($fieldId);

            if ($field->getTableId() !== $table->getId()) {
                return $this->responder->notFound($response);
            }

            $indexCollection = $this->indexRepository->getIndexesByField($field);
            $data = $this->indexTransformer->collection($indexCollection);

            return $this->responder->collection($response, $data);

        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        } catch (Exception $exception) {
            return $this->responder->error($response, $exception);
        }
    }
}
