<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: snippet and insert</title>
 </head>
 <body>
  <h1>Instruction test: snippet</h1>
  <p>First, we define the snippet:</p>

  <opt:snippet name="abc">
  	<p>Hello, my friend! The value for today is: {$value}</p>
  </opt:snippet>
  
  <p>Now, let's insert something. Insert 1:</p>
  <opt:insert snippet="abc"/>
  
  <p>Second insert. We modify the block value.</p>
  {$value is 'bar'}
  
  <opt:insert snippet="abc"/>
  
  <p>And now let's try to call an unexisting snippet:</p>
	
  <opt:insert snippet="def"/>
  
  <p>Maybe with default content:</p>
  
  <opt:insert snippet="def">
  	<p>It is nothing here.</p>
  </opt:insert>
 </body>
</html>