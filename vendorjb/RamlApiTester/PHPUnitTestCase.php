<?php

namespace JoeBengalen\RamlApiTester;

use JsonSchema\Validator;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_TestResult;

class PHPUnitTestCase extends PHPUnit_Framework_TestCase
{
    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = $this->createResult();
        }

        parent::run($result);

        $file = getcwd() . '/bin/getcoverage.php';
        $data = include $file;
        $coverage = $this->matchLocalAndRemotePaths($data);
        $result->getCodeCoverage()->append($coverage, $this);

        return $result;
    }

    /**
     * @param  array $coverage
     * @return array
     * @author Mattis Stordalen Flister <mattis@xait.no>
     */
    protected function matchLocalAndRemotePaths(array $coverage)
    {
        $coverageWithLocalPaths = array();

        foreach ($coverage as $originalRemotePath => $data) {
            $remotePath = $originalRemotePath;
            $separator  = $this->findDirectorySeparator($remotePath);

            while (!($localpath = stream_resolve_include_path($remotePath)) &&
                   strpos($remotePath, $separator) !== FALSE) {
                $remotePath = substr($remotePath, strpos($remotePath, $separator) + 1);
            }

            if ($localpath && md5_file($localpath) == $data['md5']) {
                $coverageWithLocalPaths[$localpath] = $data['coverage'];
            }
        }

        return $coverageWithLocalPaths;
    }

    /**
     * @param  string $path
     * @return string
     * @author Mattis Stordalen Flister <mattis@xait.no>
     */
    protected function findDirectorySeparator($path)
    {
        if (strpos($path, '/') !== FALSE) {
            return '/';
        }

        return '\\';
    }

    /**
     * Assert that contentType is as expected.
     *
     * @param string $expectedContentType
     * @param string $contentType
     * @param bool   $stripEncoding
     * @param string $message
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertContentType(
        $expectedContentType,
        $contentType,
        $stripEncoding = true,
        $message = ''
    ) {
        if ($stripEncoding) {
            $contentType = explode(';', $contentType)[0];
        }

        self::assertSame($expectedContentType, $contentType, $message);
    }

    /**
     * Assert that json content matches the json schema.
     *
     * @param object              $schema
     * @param string|object|array $content
     * @param string              $message
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertJsonMatchesSchema($schema, $content, $message = '')
    {
        if (is_string($schema)) {
            self::assertJson($schema, 'Invalid schema');
            $schema = json_decode($schema);
        }
        if (is_string($content)) {
            self::assertJson($content, 'Response does not contain valid json');
            $content = json_decode($content);
        }

        if (empty($message)) {
            $message = '- Property: %s, Contraint: %s, Message: %s';
        }

        $validator = new Validator();
        $validator->check($content, $schema);

        $messages = array_map(
            function ($exception) use ($message) {
                return sprintf(
                    $message,
                    $exception['property'],
                    $exception['constraint'],
                    $exception['message']
                );
            },
            $validator->getErrors()
        );

        $messages[] = '- Response: ' . json_encode($content);

        self::assertTrue($validator->isValid(), implode("\n", $messages));
    }
}
