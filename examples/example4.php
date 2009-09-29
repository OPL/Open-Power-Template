<?php 
  define('OPT_DIR', '../lib/');
  require('../lib/opt.class.php');
  
  $lang = array(
  	'global' => 
  		array(
  			'text1' => 'This is text one',
  			'text2' => 'This is text two',
  			'text3' => 'This is text three',
			'date' => 'Today is %s, good day for fishing'		
  		)
  );
 
  try{ 
    $tpl = new optClass; 
    $tpl -> root = './templates/';
    $tpl -> compile = './templates_c/';
    $tpl -> gzipCompression = 1;
    $tpl -> httpHeaders(OPT_HTML); 
    
    // init default i18n system
    $tpl -> setDefaultI18n($lang);

    $tpl -> assign('current_date', date('d.m.Y')); 
    $tpl -> parse('example4.tpl'); 
  }catch(optException $exception){ 
    optErrorHandler($exception); 
  } 
?>
