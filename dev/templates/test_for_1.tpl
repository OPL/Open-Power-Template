<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: for</title>
 </head>
 <body>
  <h1>Instruction test: for</h1>
  <p>The test of FOR instruction.</p>
  
  <ul>
  <opt:for begin="@i is 0" while="@i lt 10" iterate="@i++">
  	<li>{@i}</li>
  </opt:for>
  </ul>
 </body>
</html>
