<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Include 1</title>
 </head>
 <body>
  <h1>Test: Include 1</h1>
  <p>This file is used to test "opt:include" instruction.</p>
  
  <p>Existing template:</p>
  <opt:include file="test_included_a.tpl" />
  
  <p>Template does not exist (default content from external file):</p>
  
  <opt:include file="foo.tpl" default="test_included_def.tpl" />
  
  <p>Template does not exist (default content from the instruction):</p>
  
  <opt:include file="foo.tpl">
  	<p>Sorry, the template does not exist.</p>
  </opt:include>
 </body>
</html>
