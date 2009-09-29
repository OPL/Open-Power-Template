<html> 
<head> 
  <title>Example 6</title> 
</head> 
<body>
<h1>Example 6</h1>
<h3>Including templates</h3>
<p>Template including allows you to split the page to more smaller templates: header, footer, the body etc. This is especially useful,
when you have to use the same piece of HTML code in many places.</p>
<hr/>
{include="header.tpl"; !x; content}

<p>The page:</p>

{@content}

<i>The body of the page</i>

{include="footer.tpl"}
</body> 
</html>
