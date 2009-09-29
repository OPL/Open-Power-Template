<?php

define('DIR_MAIN', './');
define('DIR_INC', DIR_MAIN.'includes/');
define('DIR_TPL', DIR_MAIN.'templates/');
define('DIR_TPLC', DIR_MAIN.'templates_c/');
define('DIR_DATA', DIR_MAIN);
define('OPT_DIR', '../lib/');

require(OPT_DIR.'opt.class.php');
require(DIR_INC.'functions.php');
require(DIR_INC.'classes.php');
require(DIR_INC.'directives.php');

?>
