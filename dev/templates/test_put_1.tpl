<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Instruction test: put</title>
 </head>
 <body>
  <h1>Instruction test: put</h1>
  <p>This template tests the opt:put instruction, which behaves like brackets.</p>
  
  <h2>Standalone call</h2>
  <p>Hello my friend, do you need <opt:put value="$item"/>?</p>
  
  <h2>Extended call</h2>
  <p>The code below shoud create a section with a separator :)</p>

  <p><opt:put value="$s1.name" opt:section="s1" str:separator=" / "/></p>
 </body>
</html>