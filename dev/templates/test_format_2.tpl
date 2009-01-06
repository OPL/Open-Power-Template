<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Formats 2</title>
 </head>
 <body>
  <h1>Test: Formats 2</h1>
  <p>This template tests various section formats. You should see a list below.</p>
  
  <p>Current format: {$currentFormat}</p>
  
  <ol>
  <opt:section name="sect1">
  	<li>{$sect1.val} <opt:show name="sect2">
  		<ol>
  			<opt:section>
  			<li>{$sect2.val} <opt:show name="sect3">
  				<ol>
  					<opt:section>
  					<li>{$sect3.val}</li>
  					</opt:section>
  				</ol>  			
  			</opt:show></li>
  			</opt:section>
  		</ol>  	
  	</opt:show></li>
  </opt:section>
  </ol>
 </body>
</html>
