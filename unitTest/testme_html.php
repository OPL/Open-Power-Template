<?php
define('OPT_DIR', '../lib/');
set_include_path('./PHPUnit/');
require('./PHPUnit/PHPUnit/GUI/HTML.php');
require('./PHPUnit/PHPUnit.php');
require(OPT_DIR.'opt.class.php');
require('./testCases.php');

$html = new PHPUnit_GUI_HTML();

$suite = new PHPUnit_TestSuite('optTest');
$html -> addSuites(array($suite));

echo $html -> show();
?>
