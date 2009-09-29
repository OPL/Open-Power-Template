<html> 
<head> 
  <title>Example 15</title> 
</head> 
<body>
<h1>Example 15</h1>
<h3>Placing templates</h3>
<p>Instead of INCLUDE, you may also use the PLACE command, which pastes one template inside another. In this example we still "load" 
header.tpl and footer.tpl, but PLACE puts it into our template and does not generate precompiled versions for them. This is very useful,
if you work with loops, but it has also one disadvantage. If your subtemplate changes, OPT will never recompile it, because it does not know about
that.</p>
<hr/>
{place file="header.tpl" assign="tralala"}

{@tralala}

<i>The body of the page</i>

{place=footer.tpl}
</body> 
</html>
