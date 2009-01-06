<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: attribute 2</title>
  <style type="text/css"><!--
 
div.dude{
	font-weight: bold;
}

div#master{
	text-align: center;
}
  
  --></style>
 </head>
 <body>
  <h1>Instruction test: attribute</h1>
  <p>opt:attribute is used to add fully dynamic attributes to tags.</p>
  <p>Now, DIV should have several dynamic attributes.</p>
  
  <div>
  	<opt:attribute name="$attr.name" value="$attr.value" opt:section="attr"/>
  	Hi, dude!
  </div>
 </body>
</html>
