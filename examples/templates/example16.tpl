<html> 
<head> 
  <title>Example 16</title> 
</head> 
<body>
<h1>Example 16</h1>
<h3>Binding</h3>
<p>Binding works like CAPTURE, but it catches the precompiled code, so you can still change the data.</p>
<hr/>
{var=variable1; "This is value 1"}

{bind=snippet}
<font color="blue">Text: {@variable1}</font><br/>
{/bind}

{insert=snippet}
{* the variable modification will be visible, if we call the snippet again *}
{@variable1 = "This is another value"}
{insert=snippet}
</body> 
</html>
