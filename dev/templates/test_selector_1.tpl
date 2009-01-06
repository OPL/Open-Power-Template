<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Selectors 1</title>
 </head>
 <body>
  <h1>Test: Selectors 1</h1> 
  <p>This test checks the selectors.</p>
  
  <h2>Test 1</h2>
  
  <p>Ascending:</p>
  <ol>
  <opt:selector name="s1">
  	<opt:foo><li>Value: {$s1.value}</li></opt:foo>
  	<opt:bar><li><strong>Value: {$s1.value}</strong></li></opt:bar>
  </opt:selector>
  </ol>

  <p>Descending:</p>
  <ol>
  <opt:selector name="s1" order="desc">
  	<opt:foo><li>Value: {$s1.value}</li></opt:foo>
  	<opt:bar><li><strong>Value: {$s1.value}</strong></li></opt:bar>
  </opt:selector>
  </ol>

  <h2>Test 2</h2>
  <p>This test checks the selector attribute.</p>
  <ol>
  	<li opt:selector="s1">
  		<opt:foo>Value: {$s1.value}</opt:foo>
  		<opt:bar><strong>Value: {$s1.value}</strong></opt:bar>
  	</li>
  </ol>
 </body>
</html>
