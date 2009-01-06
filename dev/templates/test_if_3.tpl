<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: if</title>
 </head>
 <body>
  <h1>Instruction test: if</h1>
  <p>The test of IF instruction.</p>
  
  <opt:if test="$rand == 1">
  	<p>The random is true</p>
   <opt:elseif test="$rand eq 0">
  	<p>The random is false</p>
   </opt:elseif>
   <opt:else>
  	<p>The random is super-true</p>
   </opt:else>
  </opt:if>
 </body>
</html>
