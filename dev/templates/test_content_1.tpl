<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Content 1</title>
 </head>
 <body>
  <h1>Test: Content 1</h1>
  <p>This file tests "opt:content" attribute. If the block exists, it will be displayed as a tag
  content. Otherwise, the default content is presented.</p>
  
  <p opt:content="$existingBlock">Default tag value 1</p>
  <p opt:content="$unexistingBlock">Default tag value 2</p>
  <p opt:content="u:$htmlContent">Default tag value 3</p>
  <p opt:content="e:$htmlContent">Default tag value 4</p>
 </body>
</html>
