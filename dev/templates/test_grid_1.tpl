<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Grids 1</title>
 </head>
 <body>
  <h1>Test: Grids 1</h1> 
  <p>This test checks the grids.</p>
  
  <h2>Test 1</h2>
  
  <p>Ascending:</p>
  <table width="100%" border="1">
  	<opt:grid name="s1" cols="5">
  	<tr>
  		<opt:item>
  		<td>{$s1.item}</td>
  		</opt:item>
  		<opt:emptyItem>
  		<td>-</td>
  		</opt:emptyItem>
  	</tr>
  	</opt:grid>  
  </table>

  <p>Descending:</p>
  <table width="100%" border="1">
  	<opt:grid name="s1" cols="6" order="desc">
  	<tr>
  		<opt:item>
  		<td>{$s1.item}</td>
  		</opt:item>
  		<opt:emptyItem>
  		<td>-</td>
  		</opt:emptyItem>
  	</tr>
  	</opt:grid>  
  </table>
 </body>
</html>
