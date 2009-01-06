<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Section 2</title>
 </head>
 <body>
  <h1>Test: Section 2</h1>
  <p>"opt:show" + "opt:section" instruction tests.</p>
  
  <p>Single:</p>
  <opt:show name="foo">
  <ol>
  <opt:section>
  	<li>{$foo.block}</li>  
  </opt:section>
  </ol>
  </opt:show>
  
  <p>Nested:</p>
  <ol>
  <opt:section name="foo">
  	<li>{$foo.block}
  	<opt:show name="bar">
  	<ol>
  		<opt:section>
  		<li>{$bar.block}</li>
  		</opt:section>
  	</ol>
  	</opt:show>
  	</li>  
  </opt:section>
  </ol>
 </body>
</html>
