<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>Test: Functions 1</title>
 </head>
 <body>
  <h1>Test: Functions 1</h1>
  <p>This file tests whether various functions work.</p>
  
  <h3>firstof()</h3>
  <p>{firstof($block1, $block2, 'Default')}</p>
  
  <h3>spacify()</h3>
  <p>{spacify($smallText)}</p>
  <p>{spacify($smallText, '-')}</p>
  
  <h3>indent()</h3>
  <p>{indent($bigText, 4)}</p>
  
  <h3>strip()</h3>
  <p>{strip($stupidText)}</p>  

  <h3>truncate()</h3>
  <p>{truncate($truncText, 43)}</p>
  <p>{truncate($truncText, 43, '...')}</p>
  <p>{truncate($truncText, 43, '...', false)}</p>
    
  <h3>wordWrap()</h3>
  <p>{u:wordWrap($wrapText, 25, '&lt;br/&gt;')}</p>
  <p>{u:wordWrap($wrapText, 25, '&lt;br/&gt;', true)}</p>
  
  <h3>upper()</h3>
  <p>{upper($smallText)}</p>
  
  <h3>lower()</h3>
  <p>{lower($smallText)}</p>
 </body>
</html>
