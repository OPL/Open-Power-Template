<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<opt:root escape="no">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Escaping test</title>
 </head>
 <body>
  <h1>Escaping test</h1>
  <p>Automated escaping test. The settings are taken from the OPT configuration.</p>
  
  <p parse:test="$automated">Automated escaping as the attribute.</p>

  <p>{e:$escapeMe}</p>
  
  <p>{$dontEscapeMe}</p>
 </body>
</html>
</opt:root>