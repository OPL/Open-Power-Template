<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Sections 1</title>
 </head>
 <body>
  <h1>Test: Sections 1</h1> 
  <p>This test checks, whether the most basic functionality works. The template reads here the elements
  of the section and puts their data into template. You should see a list of five names and surnames in ascending
  (starting from Joe Smith), and then - descending order (starting from Jay Newcome).</p>
  
  <h2>Test 1</h2>
  
  <p>Ascending:</p>
  <ol>
  <opt:section name="s1">
  	<li>Name: {$s1.name} {$s1.surname}</li>
  </opt:section>
  </ol>

  <p>Descending:</p>
  <ol>
  <opt:section name="s1" order="desc">
  	<li>Name: {$s1.name} {$s1.surname}</li>
  </opt:section>
  </ol>
  
  <p>Once more ascending (the order is specified manually):</p>
  <ol>
  <opt:section name="s1" order="asc">
  	<li>Name: {$s1.name} {$s1.surname}</li>
  </opt:section>
  </ol>
  
  <h2>Test 2</h2>
  <p>This test checks, whether scalar values as section elements also work. You should see four names of different jobs.</p>
  
  <ol>
  <opt:section name="s2">
  	<li>{$s2}</li>
  </opt:section>
  </ol>

  <h2>Test 3</h2>
  <p>This test checks, whether sectionelse works.</p>
  <ol>
  <opt:section name="s3">
  	<li>{$s3.something}</li>
  	<opt:sectionelse>
  	<li>There's nothing here.</li>
  	</opt:sectionelse>
  </opt:section>
  </ol>
  
  <h3>Test 4</h3>
  <p>This test checks the intelligent section block parsing.</p>
  <ol>
  <opt:section name="s4">
  	<li>{$foo.s4.block.subitem}</li>
  </opt:section>
  </ol>

 </body>
</html>
