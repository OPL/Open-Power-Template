<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: foreach</title>
 </head>
 <body>
  <h1>Instruction test: foreach</h1>
  <p>The test of FOREACH instruction.</p>
  
   <h3>Test 1</h3>
   <p>Simple foreach</p>
   
   <ul>
   <opt:foreach array="$data" value="val">
   	<li>{@val}</li>
   </opt:foreach>
   </ul>
   
   <h3>Test 2</h3>
   <p>Quite more complicated foreach</p>
   
   <ul>
   <opt:foreach array="$data" index="idx" value="val">
   	<li>{@idx}: {@val}</li>
   </opt:foreach>
   </ul>
   
   <h3>Test 3</h3>
   <p>Parameter separator.</p>
   
   <p><opt:foreach array="$data" index="idx" value="val" str:separator=", ">{@idx}: {@val}</opt:foreach></p>
   
   <h3>Test 4</h3>
   <p>Tag separator.</p>
   
   <p><opt:foreach array="$data" index="idx" value="val"><opt:separator>, </opt:separator>{@idx}: {@val}</opt:foreach></p>
 </body>
</html>
