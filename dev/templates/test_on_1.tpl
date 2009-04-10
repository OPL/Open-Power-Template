<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Attribute test: on and if</title>
 </head>
 <body>
  <h1>Attribute test: on and if</h1>
  <p>The test of ON and IF attributes.</p>
  <p>{$rand}</p>

  <div opt:if="$rand">Is shown...</div>

  <strong opt:on="$rand">Is bold</strong>
 </body>
</html>
