<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Template test: template inheritance #7</title>
 </head>
 <body>
  <opt:insert snippet="header">
   <h1>I'm a standard header</h1>
   <p>Foo bar joe</p>  
  </opt:insert>
  
  <hr/>
  
  <!-- Insert the content of the main module -->
  <opt:insert snippet="content">
  	<p>Well, i'm also a standard content.</p>
  </opt:insert>
  
  <!-- Include additional modules here -->
  <opt:section name="modules">
  	<opt:include from="modules" branch="standalone" />
  </opt:section>
  
  <hr/>
  
  <opt:insert snippet="footer">
  	<p>And I'm a footer.</p>  
  </opt:insert>
  
  <p>&copy; Pasteright 2008 by LMAO, It seems to work!</p>
 </body>
</html>