<?php

namespace JoeBengalen\Tables\Api\Action\Field;

use Aura\Filter\Exception\FilterFailed;
use Exception;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Filter\FieldFilter;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\FieldRepository;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class UpdateFieldAction
{
    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * @var FieldFilter
     */
    protected $fieldFilter;

    /**
     * @var TableRepository
     */
    protected $tableRepository;

    /**
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * CreateFieldAction.
     *
     * @param ApiResponder    $responder
     * @param FieldFilter     $fieldFilter
     * @param TableRepository $tableRepository
     * @param FieldRepository $fieldRepository
     * @param Router          $router
     */
    public function __construct(
        ApiResponder $responder,
        FieldFilter $fieldFilter,
        TableRepository $tableRepository,
        FieldRepository $fieldRepository
    ) {
        $this->responder = $responder;
        $this->fieldFilter = $fieldFilter;
        $this->tableRepository = $tableRepository;
        $this->fieldRepository = $fieldRepository;
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
    public function __invoke(Request $request, Response $response, $tableId, $fieldId)
    {
        Assert::isNumeric($tableId);
        Assert::isNumeric($fieldId);

        try {
            $body = (array) $request->getParsedBody();
            $this->fieldFilter->assert($body);

            $table = $this->tableRepository->getTableById($tableId);
            $field = $this->fieldRepository->getFieldById($fieldId);

            if ($table->getId() !== $field->getTableId()) {
                return $this->responder->notFound($response);
            }

            $field->setName($body['name']);
            $field->setType($body['type']);
            $field->setLength($body['length']);
            $field->setAllowNull($body['allowNull']);
            $field->setDefault($body['default']);
            $field->setComment($body['comment']);
            $field->setIsPrimaryKey($body['isPrimaryKey']);
            $field->setAutoIncrement($body['autoIncrement']);

            $this->fieldRepository->updateField($field);

            return $this->responder->updated($response);

        } catch (FilterFailed $filterFailed) {
            $failures = $filterFailed->getFailures();
            return $this->responder->notValid($response, $failures);
        } catch (EntityNotFound $entityNotFound) {
            return $this->responder->notFound($response);
        } catch (DuplicateEntity $duplicateEntity) {
            return $this->responder->duplicate($response, $duplicateEntity);
        } catch (Exception $exception) {
            return $this->responder->error($response, $exception);
        }
    }
}
