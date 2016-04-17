<?php

namespace JoeBengalen\RamlApiTester;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use JoeBengalen\RamlApiTester\PHPUnitTestCase;
use JoeBengalen\RamlApiTester\RequestResponseLogger;
use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Phpdoc\PropertyPhpdoc;
use Memio\Model\Phpdoc\VariableTag;
use Memio\Model\Property;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

class TestCaseGenerator
{
    /**
     * @var Twig_Environment 
     */
    protected $twig;

    /**
     * @var File|null
     */
    protected $testFile;

    protected $target;
    protected $templateDir;

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;
    }

    protected function setupTwig()
    {
        $loader = new Twig_Loader_Filesystem($this->templateDir);
        $this->twig = new Twig_Environment($loader);

        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'export', 
                function ($var) {
                    return var_export($var, true);
                }
            )
        );
    }

    /**
     * @param TestCase[] $testCases
     */
    public function generateTestCases(array $testCases)
    {
        $this->setupTwig();
        $this->testFile = $this->buildApiTestFile('ApiTest');
        
        foreach ($testCases as $testCase) {            
            $this->generateTestCase($testCase);
        }

        $this->generateFile($this->testFile);
    }

    protected function createTestName(TestCase $testCase)
    {
        $prefix = 'test ';
        $name = $testCase->description;
        $suffix = '';

        switch ($testCase->response->code) {
            case 400:
                $suffix = ' bad request';
                break;
            case 404:
                $suffix = ' not found';
                break;
            case 409:
                $suffix = ' conflict';
                break;
        }

        return Util::camelCase($prefix . $name . $suffix);
    }

    protected function prepareRequestHeaders(Request $request)
    {
        if ($request->acceptType) {
            $request->headers['Accept'] = $request->acceptType;
        }

        if ($request->body && $request->contentType) {
            $request->headers['Content-Type'] = $request->contentType;
        }
    }

    protected function prepareResponseSchema(Response $response)
    {
        if ($response->schema) {
            $schema = json_encode(
                $response->schema->getJsonArray(),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );

            $response->schema = str_replace('\\\\', '\\\\\\', $schema);
        }
    }

    protected function extractTemplateData(TestCase $testCase)
    {
        return [
            'method' => $testCase->request->method,
            'uri' => $testCase->request->uri,
            'headers' => $testCase->request->headers,
            'body' => $testCase->request->body,
            'expectedStatusCode' => $testCase->response->code,
            'expectedContentType' => $testCase->response->contentType,
            'expectedSchema' => $testCase->response->schema,
        ];
    }

    protected function buildTestMethodBody(TestCase $testCase)
    {
        $data = $this->extractTemplateData($testCase);

        return $this->twig->render('body.twig', $data);
    }

    protected function buildTestMethod(TestCase $testCase)
    {
        $testName = $this->createTestName($testCase);
        $methodBody = $this->buildTestMethodBody($testCase);

        return Method::make($testName)->setBody($methodBody);
    }

    protected function buildSetUpMethod()
    {
        $setUpBody = <<<EOT
        RequestResponseLogger::setTargetDir('{$this->target}_out/');

        \$this->client = new Client([
            'http_errors' => false,
        ]);
EOT;
        return Method::make('setUp')->setBody($setUpBody);
    }

    protected function buildClientProperty()
    {
        $clientProperty = new Property('client');
        $clientProperty->makeProtected();
        $clientProperty->setPhpdoc(
            PropertyPhpdoc::make()->setVariableTag(
                VariableTag::make(Client::class)
            )
        );

        return $clientProperty;
    }

    protected function buildClass($className)
    {
        return Object::make("JoeBengalen\RamlApiTester\Test\\{$className}")
                ->extend(Object::make(PHPUnitTestCase::class));
    }

    protected function buildFile($fileName)
    {
        return File::make($this->target . "{$fileName}.php");
    }

    /**
     * 
     * @param string $name
     *
     * @return File
     */
    protected function buildApiTestFile($name)
    {
        $file = $this->buildFile($name);
        $class = $this->buildClass($name);
        $clientProperty = $this->buildClientProperty();
        $setUpMethod = $this->buildSetUpMethod();

        $file->setStructure($class);
        $file->addFullyQualifiedName(FullyQualifiedName::make(Client::class));
        $file->addFullyQualifiedName(FullyQualifiedName::make(GuzzleRequest::class));
        $file->addFullyQualifiedName(FullyQualifiedName::make(PHPUnitTestCase::class));
        $file->addFullyQualifiedName(FullyQualifiedName::make(RequestResponseLogger::class));

        $class->addProperty($clientProperty);
        $class->addMethod($setUpMethod);

        return $file;
    }

    public function generateTestCase(TestCase $testCase)
    {
        $this->prepareRequestHeaders($testCase->request);
        $this->prepareResponseSchema($testCase->response);

        $testMethod = $this->buildTestMethod($testCase);

        $this->testFile->getStructure()->addMethod($testMethod);
    }

    protected function generateFile(File $file)
    {
        $prettyPrinter = Build::prettyPrinter();
        $generatedCode = $prettyPrinter->generateCode($file);
        file_put_contents($file->getFilename(), $generatedCode);
    }
}
