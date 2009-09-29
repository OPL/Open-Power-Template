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
    $categoryMatch = array();
    $i = 0;
    while($row = mysql_fetch_assoc($r))
    {
    	$categories[$i] = array(
			'name' => $row['name']
		);
		$categoryMatch[$row['id']] = $i;
		$i++;
    }    
    
    $r = mysql_query('SELECT id, name, description, category FROM products ORDER BY category, id'); 
    $products = array(); 
    while($row = mysql_fetch_assoc($r)) 
    { 
      // to be clear, we split the code into more commands
      $category = $categoryMatch[$row['category']];

      // add the next item 
      $products[$category][] = array( 
          'id' => $row['id'], 
          'name' => $row['name'],
          'description' => $row['description']
       ); 
    } 

	$tpl -> assign('categories', $categories);
    $tpl -> assign('products', $products); 
    $tpl -> parse('example3.tpl'); 
    mysql_close();
  }catch(optException $exception){ 
    optErrorHandler($exception); 
  } 
?>
