<?php
define('OPT_DIR', '../lib/');
set_include_path('./PHPUnit/');
require('./PHPUnit/PHPUnit.php');
require(OPT_DIR.'opt.class.php');
require('./testCases.php');

$suite = new PHPUnit_TestSuite('optTest');
$result = PHPUnit::run($suite);
echo $result -> toString();
?>
