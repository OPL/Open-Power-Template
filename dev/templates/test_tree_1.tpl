<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Trees 1</title>
 </head>
 <body>
  <h1>Test: Trees 1</h1> 
  <p>This file tests the tree rendering. You should see a tree below.</p>
  
  <h3>Test 1</h3>
  <opt:tree name="tree">
  	<opt:list><ul><opt:content /></ul></opt:list>
  	<opt:node><li>{$tree.title} <opt:content /></li></opt:node>  
  </opt:tree>
  
  <h3>Test 2 - empty tree</h3>
  
  <opt:tree name="empty">
  	<opt:list><ul><opt:content /></ul></opt:list>
  	<opt:node><li>{$empty.title} <opt:content /></li></opt:node>  
  </opt:tree>
  
  <opt:tree name="empty">
  	<opt:list><ul><opt:content /></ul></opt:list>
  	<opt:node><li>{$empty.title} <opt:content /></li></opt:node>
  	<opt:treeelse><p>Sorry, there is no tree...</p></opt:treeelse>
  </opt:tree>
  
  <h3>Test 3 - with opt:show</h3>
  
  <opt:show name="tree">
  
  <p>This is my amazing tree:</p>
  
  <opt:tree>
  	<opt:list><ul><opt:content /></ul></opt:list>
  	<opt:node><li>{$tree.title} <opt:content /></li></opt:node>  
  </opt:tree>
  
  <opt:showelse>
  	<p>Sorry, there is no tree..</p>
  </opt:showelse>
  
  </opt:show>
 </body>
</html>
