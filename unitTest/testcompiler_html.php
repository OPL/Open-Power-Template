<?php
define('OPT_DIR', '../lib/');
set_include_path('./PHPUnit/');
require('./PHPUnit/PHPUnit/GUI/HTML.php');
require('./PHPUnit/PHPUnit.php');
require(OPT_DIR.'opt.api.php');
require(OPT_DIR.'opt.compiler.php');
require('./compilerTestCases.php');

$html = new PHPUnit_GUI_HTML();

$suite = new PHPUnit_TestSuite('optCompilerTest');
$html -> addSuites(array($suite));

echo $html -> show();
?>
