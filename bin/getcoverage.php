<?php

$coverageDirectory = dirname(__DIR__) . '/apitest/tmp/';
$coverageExtension = '.coverage';

$facade = new File_Iterator_Facade;
$files = $facade->getFilesAsArray($coverageDirectory, $coverageExtension);

$coverage = array();
foreach ($files as $file) {
    $data = unserialize(file_get_contents($file));
    unlink($file);
    unset($file);
    $filter = new PHP_CodeCoverage_Filter();
    foreach ($data as $file => $lines) {
        if ($filter->isFile($file)) {
            if (!isset($coverage[$file])) {
                $coverage[$file] = array(
                  'md5' => md5_file($file), 'coverage' => $lines
                );
            } else {
                foreach ($lines as $line => $flag) {
                    if (!isset($coverage[$file]['coverage'][$line]) ||
                        $flag > $coverage[$file]['coverage'][$line]) {
                        $coverage[$file]['coverage'][$line] = $flag;
                    }
                }
            }
        }
    }
}

return $coverage;
