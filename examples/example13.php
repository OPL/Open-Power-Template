<?php
  define('OPT_DIR', '../lib/');
  require('../lib/opt.class.php');
  try{ 
    $tpl = new optClass; 
    $tpl -> root = './templates/';
    $tpl -> compile = './templates_c/';
    $tpl -> gzipCompression = 1;
    $tpl -> httpHeaders(OPT_HTML); 

    require('db_connect.php');
    
    $r = mysql_query('SELECT id, name FROM categories ORDER BY id');
    $categories = array();
    while($row = mysql_fetch_assoc($r))
    {
    	$categories[$row['id']] = array(
			'name' => $row['name']
		);    
    }    
    
    $r = mysql_query('SELECT id, name, description, category FROM products ORDER BY category, id'); 
    $products = array(); 
    while($row = mysql_fetch_assoc($r)) 
    { 
      // add the next item 
      $products[$row['category']][] = array( 
          'id' => $row['id'], 
          'name' => $row['name'],
          'description' => $row['description']
       ); 
    } 

	$tpl -> assign('categories', $categories);
    $tpl -> assign('products', $products); 
    $tpl -> parse('example13.tpl'); 
    mysql_close();
  }catch(optException $exception){ 
    optErrorHandler($exception); 
  } 
?>
