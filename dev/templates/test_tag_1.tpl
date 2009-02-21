<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: tag 1</title>
 </head>
 <body>
  <h1>Test: tag 1</h1>
  <p>This file is used to test "opt:tag" instruction. You can choose the tag name with URL: <em>test_tag_1.php?tag=name</em></p>
  
  <opt:tag name="$tagName">This is a text</opt:tag>
  
  <opt:tag name="input" type="checkbox" single="yes">
	<opt:attribute name="name" value="foo"/>
	<opt:attribute name="checked" value="checked" opt:if="$a lt 1" />
  </opt:tag>
  
  <p>Checking whether it integrates with "opt:attribute"...</p>
  <opt:tag name="$tagName"><opt:attribute str:name="name" str:value="foo" />This is a text</opt:tag>
 </body>
</html>
