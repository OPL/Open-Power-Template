<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Formats 1</title>
 </head>
 <body>
  <h1>Test: Formats 1</h1>
  <p>This template tests if the generic format processes blocks properly.</p>
  
  <h3>Generic format</h3>
  <p>{$generic1}</p>
  <p>{$generic2.item1}</p>
  <p>{$generic2.item2.subitem}</p>
  
  <p>{$global.generic1}</p>
  <p>{$global.generic2.item1}</p>
  <p>{$global.generic2.item2.subitem}</p>
  
  {@joe is 1}
  {@var.foo is 3}
  {@var.bar is 5}
  <p>{@joe}</p>
  <p>{@var.bar}</p>
  
  <h3>Objective format</h3>
  <p>{$objective1}</p>
  <p>{$objective2.item1}</p>
  <p>{$objective2.item2.subitem}</p>
  
  <p>{$global.objective1}</p>
  <p>{$global.objective2.item1}</p>
  <p>{$global.objective2.item2.subitem}</p>
  
  <h3>Mixed format</h3>
  <p>{$mixed1}</p>
  <p>{$mixed2.item1}</p>
  <p>{$mixed2.item2.subitem}</p>
  
  <p>{$global.mixed1}</p>
  <p>{$global.mixed2.item1}</p>
  <p>{$global.mixed2.item2.subitem}</p>
 </body>
</html>
