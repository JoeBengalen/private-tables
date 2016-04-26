<?php

use JoeBengalen\HttpAuthentication\BasicAuthenticationMiddleware;
use JoeBengalen\HttpRest\RestParamsMiddleware;
use JoeBengalen\SlimCompress\GzipCompressionMiddleware;
use JoeBengalen\SlimEnvelope\EnvelopeMiddleware;
use JoeBengalen\SlimJsonp\JsonpMiddleware;
use JoeBengalen\Tables\Api\Action\ErrorAction;
use JoeBengalen\Tables\Api\Action\Field\CreateFieldAction;
use JoeBengalen\Tables\Api\Action\Field\DeleteFieldAction;
use JoeBengalen\Tables\Api\Action\Field\GetFieldAction;
use JoeBengalen\Tables\Api\Action\Field\ListFieldsAction;
use JoeBengalen\Tables\Api\Action\Field\UpdateFieldAction;
use JoeBengalen\Tables\Api\Action\HelloAction;
use JoeBengalen\Tables\Api\Action\ListAction;
use JoeBengalen\Tables\Api\Action\Table\CreateTableAction;
use JoeBengalen\Tables\Api\Action\Table\DeleteTableAction;
use JoeBengalen\Tables\Api\Action\Table\GetTableAction;
use JoeBengalen\Tables\Api\Action\Table\ListTablesAction;
use JoeBengalen\Tables\Api\Action\Table\UpdateTableAction;
use JoeBengalen\Tables\Api\Action\Test\AuthenticationAction;
use JoeBengalen\Tables\Api\Action\Test\CompressAction;
use JoeBengalen\Tables\Api\Action\Test\EnvelopeAction;
use JoeBengalen\Tables\Api\Action\Test\RestParamsAction;
use JoeBengalen\Tables\Api\App;

require '../vendor/autoload.php';

$app = new App();

// http://localhost:8888/api.php/api/v1/test/restparams?sort=-name&fields=name,id&name=test&envelope&callback=jsonpCallbackFunction

$app->add(RestParamsMiddleware::class);
$app->add(EnvelopeMiddleware::class);
$app->add(JsonpMiddleware::class);
$app->add(BasicAuthenticationMiddleware::class);
$app->add(GzipCompressionMiddleware::class);

$app->group('/api/v1', function () {
    $this->get('/hello', HelloAction::class)->setName('testConnection');
    $this->get('/list', ListAction::class)->setName('listActions');
    $this->get('/error', ErrorAction::class)->setName('errorExample');

    $this->get('/test/authentication', AuthenticationAction::class)->setName('testAuthentication');
    $this->get('/test/compress', CompressAction::class)->setName('testCompression');
    $this->get('/test/restparams', RestParamsAction::class)->setName('testRestParams');
    $this->get('/test/envelope', EnvelopeAction::class)->setName('testEnvelope');

    $this->group('/tables', function () {
        $this->get('', ListTablesAction::class)->setName('listTables');
        $this->post('', CreateTableAction::class)->setName('createTable');

        $this->group('/{tableId:[0-9]+}', function () {
            $this->get('', GetTableAction::class)->setName('getTable');
            $this->put('', UpdateTableAction::class)->setName('updateTable');
            $this->delete('', DeleteTableAction::class)->setName('deleteTable');

            $this->group('/fields', function () {
                $this->get('', ListFieldsAction::class)->setName('listFields');
                $this->post('', CreateFieldAction::class)->setName('createField');

                $this->group('/{fieldId:[0-9]+}', function () {
                    $this->get('', GetFieldAction::class)->setName('getField');
                    $this->put('', UpdateFieldAction::class)->setName('updateField');
                    $this->delete('', DeleteFieldAction::class)->setName('deleteField');

                    $this->group('/indexes', function () {
                        $this->get('', ListIndexesAction::class)->setName('listIndexes');
                        $this->post('', CreateIndexAction::class)->setName('createIndex');

                        $this->group('/{indexId:[0-9]+}', function () {
                            $this->get('', GetIndexAction::class)->setName('getIndex');
                            $this->put('', UpdateIndexAction::class)->setName('updateIndex');
                            $this->delete('', DeleteIndexAction::class)->setName('deleteIndex');
                        });
                    });

                });
            });

            $this->group('/foreign-keys', function () {
                $this->get('', ListForeignKeysAction::class)->setName('listForeignKeys');
                $this->post('', CreateForeignKeyAction::class)->setName('createForeignKey');

                $this->group('/{foreignKeyId:[0-9]+}', function () {
                    $this->get('', GetForeignKeyAction::class)->setName('getForeignKey');
                    $this->put('', UpdateForeignKeyAction::class)->setName('updateForeignKey');
                    $this->delete('', DeleteForeignKeyAction::class)->setName('deleteForeignKey');
                });
            });
        });
    });
});

$app->run();
