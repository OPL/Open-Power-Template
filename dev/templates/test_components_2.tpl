<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:root>
<opt:prolog standalone="yes"/>
<opt:dtd template="xhtml10"/>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Component test: defined components</title>
 </head>
 <body>
  <h1>Component test: defined components</h1>
  <p>The example checks, whether the defined components work properly.</p>
  
  <form method="post" action="test_components_2.php">

  <opt:snippet name="dupa">
  <com:div>
  	<p><span parse:title="$opt.component.title">{$opt.component.title}</span>: <opt:display /></p>
  	<opt:onEvent name="error">
  		<p style="color: red; ">An error occured: {$opt.component.msg}</p>
  	</opt:onEvent>
  </com:div>
  </opt:snippet>
  
  <opt:select str:name="hello1" datasource="$list" template="dupa">
  	<opt:set name="selected" value="$selected1"/>
  	<opt:set name="valid" value="$valid1"/>
  	<opt:set name="title" str:value="List 1"/>
  </opt:select>

  <opt:select str:name="hello2" datasource="$list" template="dupa">
  	<opt:set name="selected" value="$selected2"/>
  	<opt:set name="valid" value="$valid2"/>
  	<opt:set name="title" str:value="List 2"/>
  </opt:select>
  
  <p><input type="submit" value="OK"/></p>
  </form>
 </body>
</html>
</opt:root>