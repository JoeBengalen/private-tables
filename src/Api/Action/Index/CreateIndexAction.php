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
use JoeBengalen\Tables\Model\Field;
use JoeBengalen\Tables\Model\FieldRepository;
use JoeBengalen\Tables\Model\TableRepository;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class CreateFieldAction
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
     * @var Router
     */
    protected $router;

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
        FieldRepository $fieldRepository,
        Router $router
    ) {
        $this->responder = $responder;
        $this->fieldFilter = $fieldFilter;
        $this->tableRepository = $tableRepository;
        $this->fieldRepository = $fieldRepository;
        $this->router = $router;
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
     */
    public function __invoke(Request $request, Response $response, $tableId)
    {
        Assert::isNumeric($tableId);

        try {
            $body = (array) $request->getParsedBody();
            $this->fieldFilter->assert($body);

            $table = $this->tableRepository->getTableById($tableId);

            $field = new Field(
                null,
                $table->getId(),
                $body['name'],
                $body['type'],
                $body['length'],
                $body['allowNull'],
                $body['default'],
                $body['comment'],
                $body['isPrimaryKey'],
                $body['autoIncrement']
            );

            $fieldId = $this->fieldRepository->addField($field);

            $path = $this->router->pathFor('getField', [
                'tableId' => $table->getId(),
                'fieldId' => $fieldId,
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
