<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: opt:use and OPT instructions</title>
 </head>
 <body>
  <h1>Instruction test: opt:use and OPT instructions</h1>
  <p>Checking, whether opt:use attribute works with OPT instructions.</p>

  <opt:snippet name="abc">
  	<li>Name: {$abc.name} {$abc.surname}</li>
  </opt:snippet>
  
	<p>Display the list:</p>
	<ul>
		<opt:section name="s1" opt:use="abc"/>	
	</ul>
	
  
	<p>This snippet can be also linked with other section:</p>
	<ul>
		<opt:section name="s2" opt:use="abc"/>	
	</ul>
 </body>
</html>