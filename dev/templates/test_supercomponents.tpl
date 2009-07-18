<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Component test: defined components</title>
 </head>
 <body>
  <h1>Component test: defined components</h1>
  <p>The example checks, whether the defined components work properly.</p>
  <opt:supercomponent>
  <form method="post" action="test_supercomponents.php">

  <opt:select str:name="hello" datasource="$list">
  	<opt:set name="selected" value="$selected"/>
  	<opt:set name="valid" value="$valid"/>

  	<p><opt:display /></p>
  	<opt:onEvent name="error">
  		<p style="color: red; ">An error occured: {$opt.component.msg}</p>
  	</opt:onEvent>
  </opt:select>

  <p><input type="submit" value="OK"/></p>
  </form>
  </opt:supercomponent>
 </body>
</html>
