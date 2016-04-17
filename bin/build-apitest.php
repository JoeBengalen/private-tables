<?php

use JoeBengalen\RamlApiTester\TestCaseBuilder;
use JoeBengalen\RamlApiTester\TestCaseGenerator;

$root = getcwd();

require $root . '/vendor/autoload.php';

$raml = $root . '/raml/api.raml';
$apitestDir = $root . '/apitest/';
$templateDir = $root . '/vendorjb/RamlApiTester/templates/';

$testCaseBuilder = new TestCaseBuilder($raml);
$testCaseBuilder->build();

$testCaseGenerator = new TestCaseGenerator();
$testCaseGenerator->setTarget($apitestDir);
$testCaseGenerator->setTemplateDir($templateDir);
$testCaseGenerator->generateTestCases(
    $testCaseBuilder->getTestCases()
);
