<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: snippet and opt:use</title>
 </head>
 <body>
  <h1>Instruction test: snippet and opt:use</h1>
  <p>Checking, whether opt:use attribute works.</p>

  <opt:snippet name="abc">
  	<p>Hello, my friend! The value for today is: {$value}</p>  
  </opt:snippet>
  
	<div opt:use="abc">
		<p>A default content.</p>
	</div>
	
	<div opt:use="def">
		<p>Another default content.</p>
	</div>
 </body>
</html>