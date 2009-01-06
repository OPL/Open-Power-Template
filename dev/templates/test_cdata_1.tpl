<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
 <head>
  <title>CDATA test</title>
 </head>
 <body>
  <h1>CDATA test</h1>
  <p>This test is going to check, whehter the CDATA sections are correctly parsed. By default,
  OPT parses it, as XML standard wants, but sometimes it is not required. The <em>opt:literal</em>
  instruction comes then. OPT still ignores the CDATA content, but does not display its tags.</p>
  
  <p>Displaying something in a typical CDATA:</p>
  
  <p><![CDATA[Hello, friend! Do you need {$object}?]]></p>
  
  <p>Displaying something with literals.</p>
  
  <opt:literal>
  <p>Hello my friend, do you need <![CDATA[{$object}]]> or {$object}?</p>
  </opt:literal>
  
  <p>Now a bit hardcore. Alternative display modes: comments (see the source preview)</p>
  
  <opt:literal type="comment">
  <p>This will be commented in the output!</p>
  </opt:literal>
  
  <p>And "transparent". The data are escaped, but neither CDATA nor comments are displayed in the output.</p>
  
  <opt:literal type="transparent">
  <p>Hello my friend, do you need <![CDATA[{$object}]]>?</p> 
  </opt:literal>  
 </body>
</html>
