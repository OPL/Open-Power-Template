<?php
define('OPT_DIR', '../lib/');
set_include_path('./PHPUnit/');
require('./PHPUnit/PHPUnit.php');
require(OPT_DIR.'opt.api.php');
require(OPT_DIR.'opt.compiler.php');
require('./compilerTestCases.php');

$suite = new PHPUnit_TestSuite('optCompilerTest');
$result = PHPUnit::run($suite);

echo $result -> toString();
?>
