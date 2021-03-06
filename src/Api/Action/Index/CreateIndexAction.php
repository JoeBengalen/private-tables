<?php

namespace JoeBengalen\Tables\Api\Action\Index;

use Aura\Filter\Exception\FilterFailed;
use Exception;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Api\ApiResponder;
use JoeBengalen\Tables\Api\Filter\IndexFilter;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use JoeBengalen\Tables\Model\FieldRepository;
use JoeBengalen\Tables\Model\Index;
use JoeBengalen\Tables\Model\IndexRepository;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class CreateIndexAction
{
    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * @var IndexFilter
     */
    protected $indexFilter;

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
     * @var Router
     */
    protected $router;

    /**
     * CreateIndexAction.
     *
     * @param ApiResponder    $responder
     * @param IndexFilter     $indexFilter
     * @param TableRepository $tableRepository
     * @param FieldRepository $fieldRepository
     * @param IndexRepository $indexRepository
     * @param Router          $router
     */
    public function __construct(
        ApiResponder $responder,
        IndexFilter $indexFilter,
        TableRepository $tableRepository,
        FieldRepository $fieldRepository,
        IndexRepository $indexRepository,
        Router $router
    ) {
        $this->responder = $responder;
        $this->indexFilter = $indexFilter;
        $this->tableRepository = $tableRepository;
        $this->fieldRepository = $fieldRepository;
        $this->indexRepository = $indexRepository;
        $this->router = $router;
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
            $body = (array) $request->getParsedBody();
            $this->indexFilter->assert($body);

            $table = $this->tableRepository->getTableById($tableId);
            $field = $this->fieldRepository->getFieldById($fieldId);

            if ($field->getTableId() !== $table->getId()) {
                return $this->responder->notFound($response);
            }

            $index = new Index(
                null,
                $field->getId(),
                $body['name'],
                $body['unique']
            );

            $indexId = $this->indexRepository->addIndex($index);

            $path = $this->router->pathFor('getIndex', [
                'tableId' => $table->getId(),
                'fieldId' => $field->getId(),
                'indexId' => $indexId,
            ]);
            $location = $request->getUri()
                    ->withPath($path)
                    ->withQuery('')
                    ->withFragment('');

            return $this->responder->created($response, (string) $location);

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
