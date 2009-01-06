<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Cycles</title>
  <style type="text/css"><opt:literal type="comment"><![CDATA[
  
  	li.darker{
  		background: #cdab77;
  	}
  	
  	li.brighter{
  		background: #fedc89;
  	}
  
  ]]></opt:literal></style>
 </head>
 <body>
  <h1>Test: Cycles</h1> 
  <p>This file tests the cycle instruction.</p>
  
  <h2>Test</h2>
  
  <opt:cycle name="brightDark" val1="brighter" val2="darker" />
  <ol>
  <opt:section name="data">
  	<li parse:class="$opt.cycle.brightDark.next">Name: {$data.name} {$data.surname} (using "{$opt.cycle.brightDark.current}")</li>
  </opt:section>
  </ol>

 </body>
</html>