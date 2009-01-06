<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:root escaping="yes">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Inheritance 9</title>
 </head>
 <body>
  <h1>Test: Inheritance 9</h1>
  <p>This file checks whether the per-template escaping works with inheritance.</p>
  <p>Moreover, it checks the connection with "opt:root"</p>
  
  <opt:insert snippet="foo">
  	<p>This should be escaped: {$htmlContent}</p>
  </opt:insert>
 </body>
</html>
</opt:root>